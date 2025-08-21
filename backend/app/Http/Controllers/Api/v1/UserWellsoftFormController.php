<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserWellsoftFormRequest;
use App\Models\UserAccess;
use App\Models\Department;
use App\Models\User;
use App\Services\SignatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserWellsoftFormController extends Controller
{
    /**
     * The signature service instance.
     */
    private SignatureService $signatureService;

    /**
     * Create a new controller instance.
     */
    public function __construct(SignatureService $signatureService)
    {
        $this->signatureService = $signatureService;
    }

    /**
     * Submit a new Wellsoft access request from user form.
     */
    public function store(UserWellsoftFormRequest $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            
            // Check if user has STAFF role
            if (!$this->hasStaffRole($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only staff members can submit Wellsoft access requests.',
                    'error' => 'Insufficient permissions'
                ], 403);
            }

            $validatedData = $request->validated();
            
            // Update user's PF number if they don't have one
            if (!$user->pf_number || trim($user->pf_number) === '') {
                $user->pf_number = $validatedData['pf_number'];
                $user->save();
                
                Log::info('Updated user PF number', [
                    'user_id' => $user->id,
                    'pf_number' => $validatedData['pf_number']
                ]);
            }
            
            // Handle signature upload/retrieval
            $signaturePath = $this->handleSignature($validatedData, $request);
            
            if (!$signaturePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'Digital signature is required for Wellsoft access requests.',
                    'error' => 'Missing signature'
                ], 422);
            }

            // Create the Wellsoft access request
            $userAccess = UserAccess::create([
                'user_id' => $user->id,
                'pf_number' => $validatedData['pf_number'],
                'staff_name' => $validatedData['staff_name'],
                'phone_number' => $validatedData['phone_number'],
                'department_id' => $validatedData['department_id'],
                'signature_path' => $signaturePath,
                'request_type' => ['wellsoft'], // Store as array // Set request type to 'wellsoft'
                'status' => 'pending',
            ]);

            // Load relationships for response
            $userAccess->load(['user', 'department']);
            
            // Add signature info to response
            $userAccess->signature_url = $this->signatureService->getSignatureUrl($userAccess->signature_path);
            $userAccess->signature_info = $this->signatureService->getSignatureInfo($userAccess->signature_path);

            // Forward to approval workflow
            $this->initiateApprovalWorkflow($userAccess);
            
            // Log the request creation
            Log::info("Wellsoft access request created", [
                'user_id' => $user->id,
                'pf_number' => $validatedData['pf_number'],
                'request_id' => $userAccess->id,
                'department_id' => $validatedData['department_id']
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $userAccess,
                'message' => 'Wellsoft access request submitted successfully. Your request is now under review.',
                'request_id' => $userAccess->id,
                'next_steps' => [
                    'Your request will be reviewed by your Head of Department',
                    'You will receive notifications about the approval status',
                    'You can track your request status in your dashboard'
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating Wellsoft access request: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['signature'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit Wellsoft access request. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get user's Wellsoft access requests.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Check if user has STAFF role
            if (!$this->hasStaffRole($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied.',
                    'error' => 'Insufficient permissions'
                ], 403);
            }

            $query = UserAccess::with(['user', 'department'])
                ->where('user_id', $user->id)
                ->ofType('wellsoft');

            // Apply filters
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('pf_number', 'like', "%{$search}%")
                      ->orWhere('staff_name', 'like', "%{$search}%");
                });
            }

            // Sort by created_at desc by default
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate results
            $perPage = min($request->get('per_page', 10), 50);
            $requests = $query->paginate($perPage);

            // Add signature URLs to the response
            $requests->getCollection()->transform(function ($userAccess) {
                $userAccess->signature_url = $this->signatureService->getSignatureUrl($userAccess->signature_path);
                $userAccess->signature_info = $this->signatureService->getSignatureInfo($userAccess->signature_path);
                return $userAccess;
            });

            return response()->json([
                'success' => true,
                'data' => $requests,
                'message' => 'Wellsoft access requests retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving Wellsoft access requests: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Wellsoft access requests.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get a specific Wellsoft access request.
     */
    public function show(UserAccess $userAccess): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Check if user owns this request and it's a Wellsoft access request
            if ($userAccess->user_id !== $user->id || !$userAccess->hasRequestType('wellsoft')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this request.'
                ], 403);
            }

            $userAccess->load(['user', 'department']);
            
            // Add signature info
            $userAccess->signature_url = $this->signatureService->getSignatureUrl($userAccess->signature_path);
            $userAccess->signature_info = $this->signatureService->getSignatureInfo($userAccess->signature_path);

            return response()->json([
                'success' => true,
                'data' => $userAccess,
                'message' => 'Wellsoft access request retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving Wellsoft access request: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Wellsoft access request.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get available departments for the form.
     */
    public function getDepartments(): JsonResponse
    {
        try {
            // First check if departments table exists and has data
            $departments = Department::select('id', 'name', 'code')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            // If no departments found, create some basic ones
            if ($departments->isEmpty()) {
                Log::info('No departments found, creating basic departments');
                
                $basicDepartments = [
                    ['name' => 'Administration', 'code' => 'ADMIN', 'is_active' => true],
                    ['name' => 'ICT Department', 'code' => 'ICT', 'is_active' => true],
                    ['name' => 'Finance', 'code' => 'FIN', 'is_active' => true],
                    ['name' => 'Human Resources', 'code' => 'HR', 'is_active' => true],
                    ['name' => 'Medical Services', 'code' => 'MED', 'is_active' => true],
                ];
                
                foreach ($basicDepartments as $dept) {
                    try {
                        Department::firstOrCreate(
                            ['code' => $dept['code']],
                            $dept
                        );
                    } catch (\Exception $createError) {
                        Log::error('Error creating department: ' . $createError->getMessage(), $dept);
                    }
                }
                
                // Reload departments
                $departments = Department::select('id', 'name', 'code')
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get();
            }

            Log::info('Departments retrieved', ['count' => $departments->count()]);

            return response()->json([
                'success' => true,
                'data' => $departments,
                'message' => 'Departments retrieved successfully.',
                'count' => $departments->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving departments: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve departments.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'debug_info' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    /**
     * Check if signature exists for the current user's PF number.
     */
    public function checkSignature(Request $request): JsonResponse
    {
        $request->validate([
            'pf_number' => 'required|string|max:50'
        ]);

        try {
            $user = Auth::user();
            $pfNumber = $request->pf_number;
            
            // Verify that the PF number belongs to the authenticated user
            if ($user->pf_number && $user->pf_number !== $pfNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'PF Number does not match your profile.',
                    'error' => 'Invalid PF number'
                ], 422);
            }

            $signaturePath = $this->signatureService->findExistingSignature($pfNumber);
            $signatureInfo = $this->signatureService->getSignatureInfo($signaturePath);

            return response()->json([
                'success' => true,
                'data' => [
                    'pf_number' => $pfNumber,
                    'signature_exists' => !is_null($signaturePath),
                    'signature_path' => $signaturePath,
                    'signature_url' => $this->signatureService->getSignatureUrl($signaturePath),
                    'signature_info' => $signatureInfo,
                ],
                'message' => $signaturePath ? 'Signature found.' : 'No signature found.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking signature: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to check signature.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Check if user has staff role.
     */
    private function hasStaffRole(User $user): bool
    {
        return $user->role && $user->role->name === 'staff';
    }

    /**
     * Handle signature upload or retrieval.
     */
    private function handleSignature(array $validatedData, Request $request): ?string
    {
        $pfNumber = $validatedData['pf_number'];
        
        // First, try to find existing signature
        $existingSignature = $this->signatureService->findExistingSignature($pfNumber);
        
        if ($existingSignature) {
            Log::info("Using existing signature for PF: {$pfNumber}");
            return $existingSignature;
        }

        // If no existing signature and file is uploaded, store the uploaded file
        if ($request->hasFile('signature')) {
            Log::info("Storing new signature for PF: {$pfNumber}");
            return $this->signatureService->storeUploadedSignature(
                $request->file('signature'),
                $pfNumber
            );
        }

        // No signature available
        Log::warning("No signature available for PF: {$pfNumber}");
        return null;
    }

    /**
     * Initiate the approval workflow for the request.
     */
    private function initiateApprovalWorkflow(UserAccess $userAccess): void
    {
        try {
            // Get the department's HOD
            $department = $userAccess->department;
            $hod = $department->headOfDepartment ?? null;

            if ($hod) {
                // TODO: Send notification to HOD
                Log::info("Initiating approval workflow", [
                    'request_id' => $userAccess->id,
                    'request_type' => 'wellsoft',
                    'department' => $department->name,
                    'hod_id' => $hod->id,
                    'hod_name' => $hod->name
                ]);

                // TODO: Create approval queue entry
                // TODO: Send email notification to HOD
                // TODO: Create system notification
            } else {
                Log::warning("No HOD found for department", [
                    'request_id' => $userAccess->id,
                    'request_type' => 'wellsoft',
                    'department_id' => $department->id,
                    'department_name' => $department->name
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error initiating approval workflow: ' . $e->getMessage(), [
                'request_id' => $userAccess->id,
                'request_type' => 'wellsoft'
            ]);
        }
    }
}