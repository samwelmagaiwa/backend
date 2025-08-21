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
                $query->whereJsonContains('request_type', $request->request_type);
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
     * Store a newly created combined user access request.
     */
    public function store(UserAccessRequest $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            
            // Debug: Log incoming request data
            Log::info('Incoming request data:', [
                'request_type' => $request->input('request_type'),
                'services' => $request->input('services'),
                'internetPurposes' => $request->input('internetPurposes')
            ]);
            
            $validatedData = $request->validated();
            
            // Upload and store the digital signature
            $signaturePath = $this->storeSignature(
                $request->file('signature'),
                $validatedData['pf_number']
            );

            // Get selected services directly from request_type
            $selectedServices = $validatedData['request_type'];
            
            // Process internet purposes if internet access is selected
            $purposes = null;
            if (in_array('internet_access_request', $selectedServices) && isset($validatedData['internetPurposes'])) {
                $purposes = array_filter($validatedData['internetPurposes'], function($purpose) {
                    return !empty(trim($purpose));
                });
                $purposes = array_values($purposes); // Re-index array
            }
            
            Log::info('Creating combined user access request', [
                'selected_services' => $selectedServices,
                'purposes' => $purposes
            ]);
            
            // Create the combined access request - store multiple services in one row
            $userAccess = UserAccess::create([
                'user_id' => $user->id,
                'pf_number' => $validatedData['pf_number'],
                'staff_name' => $validatedData['staff_name'],
                'phone_number' => $validatedData['phone_number'],
                'department_id' => $validatedData['department_id'],
                'signature_path' => $signaturePath,
                'purpose' => $purposes,
                'request_type' => $selectedServices, // Array stored as JSON
                'status' => 'pending',
            ]);
            
            Log::info('Successfully created combined request', [
                'request_id' => $userAccess->id,
                'request_types' => $selectedServices,
                'pf_number' => $validatedData['pf_number']
            ]);

            // Load relationships for response
            $userAccess->load(['user', 'department']);
            
            // Add signature info
            $userAccess->signature_url = $this->signatureService->getSignatureUrl($userAccess->signature_path);
            $userAccess->signature_info = $this->signatureService->getSignatureInfo($userAccess->signature_path);
            
            // Forward to appropriate queues for each service type
            foreach ($selectedServices as $serviceType) {
                $this->forwardToHodQueue($userAccess, $serviceType);
            }
            
            Log::info("Combined user access request created", [
                'user_id' => $user->id,
                'request_id' => $userAccess->id,
                'pf_number' => $validatedData['pf_number'],
                'request_types' => $selectedServices,
                'service_count' => count($selectedServices),
                'has_purposes' => !empty($purposes)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $userAccess,
                'message' => 'Combined access request submitted successfully with ' . count($selectedServices) . ' service type(s).',
                'signature_info' => $this->signatureService->getSignatureInfo($signaturePath),
                'services_requested' => $selectedServices
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating combined user access request: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['signature']),
                'error_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit combined access request.',
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
     * Store uploaded signature file.
     */
    private function storeSignature($signatureFile, string $pfNumber): string
    {
        try {
            // Create directory path based on PF number
            $directory = 'signatures/' . strtoupper($pfNumber);
            
            // Generate unique filename
            $filename = 'signature_' . time() . '.' . $signatureFile->getClientOriginalExtension();
            
            // Store file in storage/app/public/signatures/{PF_NUMBER}/
            $path = $signatureFile->storeAs($directory, $filename, 'public');
            
            Log::info('Signature uploaded successfully', [
                'pf_number' => $pfNumber,
                'filename' => $filename,
                'path' => $path
            ]);
            
            return $path;
            
        } catch (\Exception $e) {
            Log::error('Error storing signature: ' . $e->getMessage(), [
                'pf_number' => $pfNumber
            ]);
            throw $e;
        }
    }



    /**
     * Forward request to HOD queue based on request type.
     */
    private function forwardToHodQueue(UserAccess $userAccess, string $serviceType = null): void
    {
        try {
            // If no specific service type provided, process all request types
            if ($serviceType) {
                $requestTypes = [$serviceType];
            } else {
                // Get request types from the model (already handled as array by casting)
                $requestTypes = $userAccess->getRequestTypesArray();
            }
            
            Log::info("Processing request types for HOD queue", [
                'request_id' => $userAccess->id,
                'service_type_param' => $serviceType,
                'request_types_to_process' => $requestTypes
            ]);
            
            foreach ($requestTypes as $requestType) {
                $queueName = $this->getHodQueueName($requestType);
                
                Log::info("Forwarding request to HOD queue", [
                    'request_id' => $userAccess->id,
                    'request_type' => $requestType,
                    'queue_name' => $queueName,
                    'department_id' => $userAccess->department_id
                ]);

                // TODO: Implement actual queue dispatch
                // dispatch(new ProcessUserAccessRequest($userAccess, $requestType))->onQueue($queueName);
            }
            
        } catch (\Exception $e) {
            Log::error('Error forwarding to HOD queue: ' . $e->getMessage(), [
                'request_id' => $userAccess->id,
                'service_type' => $serviceType,
                'error_trace' => $e->getTraceAsString()
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