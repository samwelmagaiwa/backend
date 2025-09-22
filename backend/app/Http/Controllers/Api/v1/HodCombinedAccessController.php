<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\UserAccess;
use App\Models\Department;
use App\Traits\HandlesStatusQueries;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HodCombinedAccessController extends Controller
{
    use HandlesStatusQueries;
    /**
     * Get all combined access requests for HOD approval
     */
    public function index(Request $request): JsonResponse
    {
        try {
            Log::info('HOD Combined Access: Fetching requests', [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Unknown',
                'user_email' => auth()->user()->email ?? 'Unknown',
                'filters' => $request->all()
            ]);

            $query = UserAccess::with(['user', 'department'])
                ->whereNotNull('request_type');
                // Show ALL requests regardless of status - HODs should see the complete history
                // including pending, approved, rejected, implemented, and completed requests

            // DEPARTMENT FILTERING: HOD only sees requests from their department(s)
            $currentUser = auth()->user();
            $hodDepartmentIds = $currentUser->departmentsAsHOD()->pluck('id')->toArray();
            
            if (!empty($hodDepartmentIds)) {
                $query->whereIn('department_id', $hodDepartmentIds);
                Log::info('HOD Department Filter Applied', [
                    'user_id' => $currentUser->id,
                    'user_name' => $currentUser->name,
                    'user_email' => $currentUser->email,
                    'hod_department_ids' => $hodDepartmentIds,
                    'requests_before_dept_filter' => UserAccess::with(['user', 'department'])
                        ->whereNotNull('request_type')
                        ->where(function ($q) {
                            $q->where('status', 'pending')
                                ->orWhere('status', 'pending_hod')
                                ->orWhere('status', 'hod_approved')
                                ->orWhere('status', 'hod_rejected');
                        })->count(),
                    'requests_after_dept_filter' => (clone $query)->count()
                ]);
            } else {
                // If user is not HOD of any department, show no requests
                Log::warning('HOD Access Attempt: User is not HOD of any department', [
                    'user_id' => $currentUser->id,
                    'user_name' => $currentUser->name,
                    'user_email' => $currentUser->email,
                    'user_roles' => $currentUser->roles()->pluck('name')->toArray() ?? 'No roles loaded'
                ]);
                $query->whereRaw('1 = 0'); // This will return no results
            }

            // Apply filters
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('staff_name', 'like', "%{$searchTerm}%")
                        ->orWhere('pf_number', 'like', "%{$searchTerm}%")
                        ->orWhereHas('department', function ($dq) use ($searchTerm) {
                            $dq->where('name', 'like', "%{$searchTerm}%");
                        })
                        ->orWhere('id', 'like', "%{$searchTerm}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('department')) {
                $query->where('department_id', $request->department);
            }

            // Order by created_at (FIFO - oldest first)
            $query->orderBy('created_at', 'asc');

            $perPage = $request->get('per_page', 50);
            $requests = $query->paginate($perPage);

            // Transform the data for frontend
            $transformedData = $requests->through(function ($request) {
                return $this->transformRequestData($request);
            });

            Log::info('HOD Combined Access: Requests retrieved successfully', [
                'count' => $requests->total()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Combined access requests retrieved successfully',
                'data' => $transformedData
            ]);

        } catch (\Exception $e) {
            Log::error('HOD Combined Access: Error fetching requests', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve combined access requests',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get a specific combined access request by ID
     */
    public function show($id): JsonResponse
    {
        try {
            Log::info('HOD Combined Access: Fetching specific request', [
                'request_id' => $id,
                'user_id' => auth()->id()
            ]);

            $request = UserAccess::with(['user', 'department'])
                ->findOrFail($id);

            // DEPARTMENT AUTHORIZATION: Ensure HOD can only access requests from their department(s)
            $currentUser = auth()->user();
            $hodDepartmentIds = $currentUser->departmentsAsHOD()->pluck('id')->toArray();
            
            if (!empty($hodDepartmentIds) && !in_array($request->department_id, $hodDepartmentIds)) {
                Log::warning('HOD Access Denied: Request not from HOD department', [
                    'user_id' => $currentUser->id,
                    'request_department_id' => $request->department_id,
                    'hod_department_ids' => $hodDepartmentIds
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. You can only view requests from your department.',
                    'error' => 'Unauthorized access'
                ], 403);
            }

            $transformedData = $this->transformRequestData($request);

            Log::info('HOD Combined Access: Request retrieved successfully', [
                'request_id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request retrieved successfully',
                'data' => $transformedData
            ]);

        } catch (\Exception $e) {
            Log::error('HOD Combined Access: Error fetching request', [
                'request_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Request not found',
                'error' => app()->environment('local') ? $e->getMessage() : 'Request not found'
            ], 404);
        }
    }

    /**
     * Update HOD approval status with module form data handling
     */
    public function updateApproval(Request $request, $id): JsonResponse
    {
        try {
            Log::info('HOD Combined Access: Updating approval', [
                'request_id' => $id,
                'user_id' => auth()->id(),
                'approval_data' => $request->except(['wellsoft_modules', 'jeeva_modules']) // Don't log large arrays
            ]);

            $userAccessRequest = UserAccess::findOrFail($id);

            // DEPARTMENT AUTHORIZATION: Ensure HOD can only approve requests from their department(s)
            $currentUser = auth()->user();
            $hodDepartmentIds = $currentUser->departmentsAsHOD()->pluck('id')->toArray();
            
            if (!empty($hodDepartmentIds) && !in_array($userAccessRequest->department_id, $hodDepartmentIds)) {
                Log::warning('HOD Approval Denied: Request not from HOD department', [
                    'user_id' => $currentUser->id,
                    'request_id' => $id,
                    'request_department_id' => $userAccessRequest->department_id,
                    'hod_department_ids' => $hodDepartmentIds
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. You can only approve requests from your department.',
                    'error' => 'Unauthorized access'
                ], 403);
            }

            // Validate the approval data including module selections
            $validatedData = $request->validate([
                'hod_status' => 'required|in:approved,rejected',
                'hod_comments' => 'nullable|string|max:1000',
                'hod_name' => 'nullable|string|max:255',
                'hod_approved_at' => 'nullable|string',
                // Module form data
                'module_requested_for' => 'nullable|in:use,revoke',
                'wellsoft_modules' => 'nullable|array',
                'wellsoft_modules.*' => 'string',
                'jeeva_modules' => 'nullable|array', 
                'jeeva_modules.*' => 'string',
                'internet_purposes' => 'nullable|array',
                'internet_purposes.*' => 'string',
                'access_type' => 'nullable|in:permanent,temporary',
                'temporary_until' => 'nullable|date|after:today',
            ]);

            DB::beginTransaction();

            // Update the request - automatically capture authenticated user's name
            $currentUser = auth()->user();
            $updateData = [
                'status' => $validatedData['hod_status'] === 'approved' ? 'hod_approved' : 'hod_rejected',
                'hod_status' => $validatedData['hod_status'], // Set the new hod_status column
                'hod_comments' => $validatedData['hod_comments'] ?? '',
                'hod_name' => $currentUser->name, // Always use authenticated user's name
                'hod_approved_by' => $currentUser->id,
                'hod_approved_by_name' => $currentUser->name,
                'hod_approved_at' => now(),
                'updated_at' => now()
            ];
            
            // Add module form data if provided
            if (isset($validatedData['module_requested_for'])) {
                $updateData['module_requested_for'] = $validatedData['module_requested_for'];
            }
            if (isset($validatedData['wellsoft_modules'])) {
                $updateData['wellsoft_modules_selected'] = array_values(array_filter($validatedData['wellsoft_modules']));
            }
            if (isset($validatedData['jeeva_modules'])) {
                $updateData['jeeva_modules_selected'] = array_values(array_filter($validatedData['jeeva_modules']));
            }
            if (isset($validatedData['internet_purposes'])) {
                $updateData['internet_purposes'] = array_values(array_filter($validatedData['internet_purposes']));
            }
            if (isset($validatedData['access_type'])) {
                $updateData['access_type'] = $validatedData['access_type'];
            }
            if (isset($validatedData['temporary_until'])) {
                $updateData['temporary_until'] = $validatedData['temporary_until'];
            }

            $userAccessRequest->update($updateData);

            DB::commit();

            Log::info('HOD Combined Access: Approval updated successfully with module data', [
                'request_id' => $id,
                'status' => $validatedData['hod_status'],
                'wellsoft_modules_count' => count($validatedData['wellsoft_modules'] ?? []),
                'jeeva_modules_count' => count($validatedData['jeeva_modules'] ?? []),
                'internet_purposes_count' => count($validatedData['internet_purposes'] ?? []),
                'module_requested_for' => $validatedData['module_requested_for'] ?? 'not_specified'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request approval updated successfully with module selections',
                'data' => $this->transformRequestData($userAccessRequest->fresh())
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('HOD Combined Access: Error updating approval', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update request approval',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Cancel a combined access request
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            Log::info('HOD Combined Access: Cancelling request', [
                'request_id' => $id,
                'user_id' => auth()->id()
            ]);

            $userAccessRequest = UserAccess::findOrFail($id);

            $validatedData = $request->validate([
                'reason' => 'required|string|max:1000'
            ]);

            DB::beginTransaction();

            $userAccessRequest->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validatedData['reason'],
                'cancelled_by' => auth()->id(),
                'cancelled_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            Log::info('HOD Combined Access: Request cancelled successfully', [
                'request_id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request cancelled successfully',
                'data' => $this->transformRequestData($userAccessRequest->fresh())
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('HOD Combined Access: Error cancelling request', [
                'request_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel request',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get statistics for HOD dashboard
     */
    public function statistics(): JsonResponse
    {
        try {
            Log::info('HOD Combined Access: Fetching statistics', [
                'user_id' => auth()->id()
            ]);

            // DEPARTMENT FILTERING: HOD statistics only for their department(s)
            $currentUser = auth()->user();
            $hodDepartmentIds = $currentUser->departmentsAsHOD()->pluck('id')->toArray();
            
            $baseQuery = UserAccess::query();
            if (!empty($hodDepartmentIds)) {
                $baseQuery->whereIn('department_id', $hodDepartmentIds);
            } else {
                // If not HOD of any department, return zero stats
                $baseQuery->whereRaw('1 = 0');
            }
            
            $stats = [
                'pendingHod' => (clone $baseQuery)->where(function($q) { $q->whereNull('hod_status')->orWhere('hod_status', 'pending'); })->count(),
                'hodApproved' => (clone $baseQuery)->where('hod_status', 'approved')->count(),
                'hodRejected' => (clone $baseQuery)->where('hod_status', 'rejected')->count(),
                'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
                'implemented' => (clone $baseQuery)->where('status', 'implemented')->count(),
                'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
                'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
                'total' => (clone $baseQuery)->count(),
                'thisMonth' => (clone $baseQuery)->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                'lastMonth' => (clone $baseQuery)->whereMonth('created_at', Carbon::now()->subMonth()->month)
                    ->whereYear('created_at', Carbon::now()->subMonth()->year)
                    ->count(),
                'departments' => !empty($hodDepartmentIds) ? Department::whereIn('id', $hodDepartmentIds)->pluck('name')->toArray() : []
            ];

            Log::info('HOD Combined Access: Statistics retrieved successfully', $stats);

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('HOD Combined Access: Error fetching statistics', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'data' => [
                    'pendingHod' => 0,
                    'hodApproved' => 0,
                    'hodRejected' => 0,
                    'approved' => 0,
                    'implemented' => 0,
                    'completed' => 0,
                    'cancelled' => 0,
                    'total' => 0,
                    'thisMonth' => 0,
                    'lastMonth' => 0
                ]
            ]);
        }
    }

    /**
     * Transform request data for frontend consumption
     */
    private function transformRequestData($request): array
    {
        return [
            'id' => $request->id,
            'request_id' => 'REQ-' . str_pad($request->id, 6, '0', STR_PAD_LEFT),
            'pf_number' => $request->pf_number,
            'staff_name' => $request->staff_name,
            'full_name' => $request->staff_name, // Alias for compatibility
            'phone' => $request->phone_number,
            'phone_number' => $request->phone_number,
            'department' => $request->department?->name ?? 'N/A',
            'department_id' => $request->department_id,
            'department_name' => $request->department?->name ?? 'N/A',
            'request_type' => $request->getRequestTypesArray(),
            'request_type_display' => $request->request_type_name,
            'request_types' => $request->getRequestTypesArray(), // Array format
            'purpose' => $request->purpose,
            
            // NEW: Include module data for conditional display
            'wellsoft_modules' => $request->wellsoft_modules,
            'jeeva_modules' => $request->jeeva_modules,
            'wellsoft_modules_selected' => $request->wellsoft_modules_selected ?? [],
            'jeeva_modules_selected' => $request->jeeva_modules_selected ?? [],
            'internet_purposes' => $request->internet_purposes,
            'module_requested_for' => $request->module_requested_for ?? 'use',
            'access_type' => $request->access_type,
            'temporary_until' => $request->temporary_until?->format('Y-m-d'),
            
            'status' => $request->status,
            'hod_status' => $request->status,  // Alias for frontend
            'status_display' => $request->status_name,
            'signature_path' => $request->signature_path,
            'created_at' => $request->created_at,
            'updated_at' => $request->updated_at,
            'submission_date' => $request->created_at,
            'hod_comments' => $request->hod_comments ?? '',
            'hod_name' => $request->hod_name ?? '',
            'hod_approved_at' => $request->hod_approved_at,
            // Approval workflow status
            'hod_approval_status' => $this->getApprovalStatus($request, 'hod'),
            'divisional_approval_status' => $this->getApprovalStatus($request, 'divisional'),
            'dict_approval_status' => $this->getApprovalStatus($request, 'dict'),
            'head_it_approval_status' => $this->getApprovalStatus($request, 'head_it'),
            'ict_approval_status' => $this->getApprovalStatus($request, 'ict'),
        ];
    }

    /**
     * Get approval status for a specific role using new status columns
     */
    private function getApprovalStatus($request, $role): string
    {
        switch ($role) {
            case 'hod':
                return $request->hod_status ?? 'pending';
            case 'divisional':
                return $request->divisional_status ?? 'pending';
            case 'dict':
            case 'ict_director':
                return $request->ict_director_status ?? 'pending';
            case 'head_it':
                return $request->head_it_status ?? 'pending';
            case 'ict':
            case 'ict_officer':
                return $request->ict_officer_status ?? 'pending';
            default:
                return 'pending';
        }
    }
}
