<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAccessRequest;
use App\Models\UserAccess;
use App\Models\Department;
use App\Services\SignatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserAccessController extends Controller
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
        $this->middleware('auth:sanctum');
        $this->signatureService = $signatureService;
    }

    /**
     * Display a listing of user access requests.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = UserAccess::with(['user', 'department'])
                ->where('user_id', Auth::id());

            // Apply filters
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            if ($request->has('request_type') && $request->request_type !== '') {
                $query->where('request_type', $request->request_type);
            }

            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('pf_number', 'like', "%{$search}%")
                      ->orWhere('staff_name', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%");
                });
            }

            // Sort by created_at desc by default
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate results
            $perPage = min($request->get('per_page', 15), 100); // Max 100 per page
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
                'message' => 'User access requests retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving user access requests: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user access requests.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created user access request.
     */
    public function store(UserAccessRequest $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            $validatedData = $request->validated();
            
            // Get signature path (existing or uploaded)
            $signaturePath = $this->signatureService->getSignaturePath(
                $validatedData['pf_number'],
                $request->file('digital_signature')
            );

            $createdRequests = [];
            $requestTypes = $validatedData['request_type'];

            // Create a separate record for each request type
            foreach ($requestTypes as $requestType) {
                $userAccess = UserAccess::create([
                    'user_id' => $user->id,
                    'pf_number' => $validatedData['pf_number'],
                    'staff_name' => $validatedData['staff_name'],
                    'phone_number' => $validatedData['phone_number'],
                    'department_id' => $validatedData['department_id'],
                    'signature_path' => $signaturePath,
                    'request_type' => $requestType,
                    'status' => 'pending',
                ]);

                // Load relationships for response
                $userAccess->load(['user', 'department']);
                
                // Add signature info
                $userAccess->signature_url = $this->signatureService->getSignatureUrl($userAccess->signature_path);
                $userAccess->signature_info = $this->signatureService->getSignatureInfo($userAccess->signature_path);
                
                $createdRequests[] = $userAccess;

                // Forward to HOD queue based on request type
                $this->forwardToHodQueue($userAccess);
                
                Log::info("User access request created", [
                    'user_id' => $user->id,
                    'pf_number' => $validatedData['pf_number'],
                    'request_type' => $requestType,
                    'request_id' => $userAccess->id
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $createdRequests,
                'message' => count($createdRequests) > 1 
                    ? 'User access requests submitted successfully.' 
                    : 'User access request submitted successfully.',
                'signature_info' => $signaturePath ? $this->signatureService->getSignatureInfo($signaturePath) : null
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating user access request: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['digital_signature'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit user access request.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified user access request.
     */
    public function show(UserAccess $userAccess): JsonResponse
    {
        try {
            // Check if user owns this request
            if ($userAccess->user_id !== Auth::id()) {
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
                'message' => 'User access request retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving user access request: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user access request.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update the specified user access request.
     */
    public function update(UserAccessRequest $request, UserAccess $userAccess): JsonResponse
    {
        try {
            // Check if user owns this request
            if ($userAccess->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this request.'
                ], 403);
            }

            // Check if request can be updated (only pending requests)
            if (!$userAccess->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be updated.'
                ], 422);
            }

            $validatedData = $request->validated();
            
            // Handle signature update if new file uploaded
            if ($request->hasFile('digital_signature')) {
                // Delete old signature if it exists and is not shared
                if ($userAccess->signature_path) {
                    $this->signatureService->deleteSignature($userAccess->signature_path);
                }
                
                // Store new signature
                $signaturePath = $this->signatureService->storeUploadedSignature(
                    $request->file('digital_signature'),
                    $validatedData['pf_number']
                );
                $validatedData['signature_path'] = $signaturePath;
            }

            // Update the request
            $userAccess->update([
                'pf_number' => $validatedData['pf_number'],
                'staff_name' => $validatedData['staff_name'],
                'phone_number' => $validatedData['phone_number'],
                'department_id' => $validatedData['department_id'],
                'signature_path' => $validatedData['signature_path'] ?? $userAccess->signature_path,
            ]);

            $userAccess->load(['user', 'department']);
            
            // Add signature info
            $userAccess->signature_url = $this->signatureService->getSignatureUrl($userAccess->signature_path);
            $userAccess->signature_info = $this->signatureService->getSignatureInfo($userAccess->signature_path);

            Log::info("User access request updated", [
                'user_id' => Auth::id(),
                'request_id' => $userAccess->id
            ]);

            return response()->json([
                'success' => true,
                'data' => $userAccess,
                'message' => 'User access request updated successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating user access request: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user access request.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified user access request.
     */
    public function destroy(UserAccess $userAccess): JsonResponse
    {
        try {
            // Check if user owns this request
            if ($userAccess->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this request.'
                ], 403);
            }

            // Check if request can be deleted (only pending requests)
            if (!$userAccess->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be deleted.'
                ], 422);
            }

            $userAccess->delete();

            Log::info("User access request deleted", [
                'user_id' => Auth::id(),
                'request_id' => $userAccess->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User access request deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting user access request: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user access request.',
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
            $departments = Department::select('id', 'name', 'code')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $departments,
                'message' => 'Departments retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving departments: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve departments.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Check if signature exists for a PF number.
     */
    public function checkSignature(Request $request): JsonResponse
    {
        $request->validate([
            'pf_number' => 'required|string|max:50'
        ]);

        try {
            $pfNumber = $request->pf_number;
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
     * Forward request to HOD queue based on request type.
     */
    private function forwardToHodQueue(UserAccess $userAccess): void
    {
        try {
            // This is where you would implement the queue logic
            // For now, we'll just log the action
            
            $queueName = $this->getHodQueueName($userAccess->request_type);
            
            Log::info("Forwarding request to HOD queue", [
                'request_id' => $userAccess->id,
                'request_type' => $userAccess->request_type,
                'queue_name' => $queueName,
                'department_id' => $userAccess->department_id
            ]);

            // TODO: Implement actual queue dispatch
            // dispatch(new ProcessUserAccessRequest($userAccess))->onQueue($queueName);
            
        } catch (\Exception $e) {
            Log::error('Error forwarding to HOD queue: ' . $e->getMessage(), [
                'request_id' => $userAccess->id
            ]);
        }
    }

    /**
     * Get HOD queue name based on request type.
     */
    private function getHodQueueName(string $requestType): string
    {
        $queueMap = [
            'jeeva_access' => 'hod_jeeva_queue',
            'wellsoft' => 'hod_wellsoft_queue',
            'internet_access_request' => 'hod_internet_queue',
        ];

        return $queueMap[$requestType] ?? 'hod_default_queue';
    }
}