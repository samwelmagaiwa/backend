<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\UserAccess;
use App\Models\Department;
use App\Services\StatusMigrationService;
use App\Traits\HandlesStatusQueries;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DictCombinedAccessController extends Controller
{
    use HandlesStatusQueries;
    
    protected $statusMigrationService;
    
    public function __construct(StatusMigrationService $statusMigrationService)
    {
        $this->statusMigrationService = $statusMigrationService;
    }
    /**
     * Get all Divisional Director-approved combined access requests for ICT Director approval
     */
    public function index(Request $request): JsonResponse
    {
        try {
            Log::info('ICT Director Combined Access: Fetching requests', [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Unknown',
                'user_email' => auth()->user()->email ?? 'Unknown',
                'filters' => $request->all()
            ]);

            // ICT Director sees requests using new granular status system:
            // 1. Requests where HOD and Divisional have approved, pending ICT Director approval
            // 2. Requests already processed by ICT Director (for history tracking)
            $query = UserAccess::with(['user', 'department'])
                ->whereNotNull('request_type')
                ->where('hod_status', 'approved') // Must have HOD approval
                ->where('divisional_status', 'approved') // Must have Divisional approval
                ->where(function ($q) {
                    $q->where('ict_director_status', 'pending') // Pending ICT Director approval
                      ->orWhere('ict_director_status', 'approved') // Already approved by ICT Director
                      ->orWhere('ict_director_status', 'rejected'); // Already rejected by ICT Director
                });

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
                // Map frontend status values to appropriate granular status filtering
                $statusFilter = $request->status;
                
                switch ($statusFilter) {
                    case 'pending':
                        $query->where('ict_director_status', 'pending');
                        break;
                    case 'approved':
                        $query->where('ict_director_status', 'approved');
                        break;
                    case 'rejected':
                        $query->where('ict_director_status', 'rejected');
                        break;
                    default:
                        // For backward compatibility, also check legacy status column
                        $query->where(function($q) use ($statusFilter) {
                            $q->where('ict_director_status', $statusFilter)
                              ->orWhere('status', $statusFilter);
                        });
                        break;
                }
            }

            if ($request->filled('department')) {
                $query->where('department_id', $request->department);
            }

            // Order by divisional approval date (FIFO - oldest divisional approval first)
            // Also order by ICT Director status (pending first for action items)
            $query->orderByRaw("CASE WHEN ict_director_status = 'pending' THEN 0 ELSE 1 END")
                  ->orderBy('divisional_approved_at', 'asc');

            $perPage = $request->get('per_page', 50);
            $requests = $query->paginate($perPage);

            // Transform the data for frontend
            $transformedData = $requests->through(function ($request) {
                return $this->transformRequestData($request);
            });

            Log::info('ICT Director Combined Access: Requests retrieved successfully', [
                'count' => $requests->total()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Divisional Director-approved combined access requests retrieved successfully',
                'data' => $transformedData
            ]);

        } catch (\Exception $e) {
            Log::error('ICT Director Combined Access: Error fetching requests', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Divisional Director-approved combined access requests',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get a specific Divisional Director-approved combined access request by ID
     */
    public function show($id): JsonResponse
    {
        try {
            Log::info('ICT Director Combined Access: Fetching specific request', [
                'request_id' => $id,
                'user_id' => auth()->id()
            ]);

            $request = UserAccess::with(['user', 'department'])
                ->findOrFail($id);

            // ICT Director can view all requests (for complete visibility and history)
            $transformedData = $this->transformRequestData($request);

            Log::info('ICT Director Combined Access: Request retrieved successfully', [
                'request_id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request retrieved successfully',
                'data' => $transformedData
            ]);

        } catch (\Exception $e) {
            Log::error('ICT Director Combined Access: Error fetching request', [
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
     * Update ICT Director approval status
     */
    public function updateApproval(Request $request, $id): JsonResponse
    {
        try {
            Log::info('ICT Director Combined Access: Updating approval', [
                'request_id' => $id,
                'user_id' => auth()->id(),
                'approval_data' => $request->all()
            ]);

            $userAccessRequest = UserAccess::findOrFail($id);

            // Ensure request is available for ICT Director approval using new status columns
            // Check that HOD and Divisional have approved and ICT Director is pending
            if ($userAccessRequest->hod_status !== 'approved' || 
                $userAccessRequest->divisional_status !== 'approved' ||
                $userAccessRequest->ict_director_status !== 'pending') {
                    
                Log::warning('ICT Director Approval Denied: Invalid approval workflow state', [
                    'request_id' => $id,
                    'hod_status' => $userAccessRequest->hod_status,
                    'divisional_status' => $userAccessRequest->divisional_status,
                    'ict_director_status' => $userAccessRequest->ict_director_status
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Request must have HOD and Divisional approval before ICT Director can process it.',
                    'error' => 'Invalid workflow state for ICT Director approval',
                    'hod_status' => $userAccessRequest->hod_status,
                    'divisional_status' => $userAccessRequest->divisional_status,
                    'ict_director_status' => $userAccessRequest->ict_director_status
                ], 400);
            }

            // Validate the approval data
            $validatedData = $request->validate([
                'dict_status' => 'required|in:approved,rejected',
                'dict_comments' => 'nullable|string|max:1000',
                'dict_name' => 'nullable|string|max:255',
                'dict_approved_at' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Update the request - automatically capture authenticated user's name
            $currentUser = auth()->user();
            $updateData = [
                'status' => $validatedData['dict_status'] === 'approved' ? 'dict_approved' : 'dict_rejected',
                'ict_director_status' => $validatedData['dict_status'], // Set the new ict_director_status column
                'dict_name' => $currentUser->name, // Always use authenticated user's name
                'dict_approved_by' => $currentUser->id,
                'dict_approved_by_name' => $currentUser->name,
                'dict_approved_at' => now(),
                'updated_at' => now()
            ];
            
            // Store comments in appropriate field based on decision
            if ($validatedData['dict_status'] === 'rejected') {
                // For rejections, store in the rejection_reasons field
                $updateData['ict_director_rejection_reasons'] = $validatedData['dict_comments'] ?? '';
                $updateData['dict_comments'] = null; // Clear comments field for rejections
                $updateData['ict_director_comments'] = null; // Clear ICT director comments field
            } else {
                // For approvals/recommendations, store in comments fields
                $updateData['dict_comments'] = $validatedData['dict_comments'] ?? '';
                $updateData['ict_director_comments'] = $validatedData['dict_comments'] ?? '';
                $updateData['ict_director_rejection_reasons'] = null; // Clear rejection reasons for approvals
            }

            $userAccessRequest->update($updateData);

            DB::commit();

            Log::info('ICT Director Combined Access: Approval updated successfully', [
                'request_id' => $id,
                'status' => $validatedData['dict_status']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request approval updated successfully',
                'data' => $this->transformRequestData($userAccessRequest->fresh())
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('ICT Director Combined Access: Error updating approval', [
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
     * Cancel a combined access request (ICT Director action)
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            Log::info('ICT Director Combined Access: Cancelling request', [
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

            Log::info('ICT Director Combined Access: Request cancelled successfully', [
                'request_id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request cancelled successfully',
                'data' => $this->transformRequestData($userAccessRequest->fresh())
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('ICT Director Combined Access: Error cancelling request', [
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
     * Get statistics for ICT Director dashboard
     */
    public function statistics(): JsonResponse
    {
        try {
            Log::info('ICT Director Combined Access: Fetching statistics', [
                'user_id' => auth()->id()
            ]);

            // ICT Director sees statistics using new granular status columns
            $baseQuery = UserAccess::query();
            
            // Use trait method to get comprehensive statistics
            $systemStats = $this->getSystemStatistics();
            
            $stats = [
                // Overall workflow statistics
                'pending' => $this->getPendingRequestsForStage('ict_director')->count(),
                'hodApproved' => (clone $baseQuery)->where('hod_status', 'approved')->count(),
                'divisionalApproved' => (clone $baseQuery)->where('hod_status', 'approved')
                                                            ->where('divisional_status', 'approved')->count(),
                'pendingDict' => $this->getPendingRequestsForStage('ict_director')->count(),
                'dictApproved' => (clone $baseQuery)->where('ict_director_status', 'approved')->count(),
                'dictRejected' => (clone $baseQuery)->where('ict_director_status', 'rejected')->count(),
                
                // Final states
                'approved' => $this->getCompletedRequests()->count(),
                'implemented' => (clone $baseQuery)->where('ict_officer_status', 'implemented')->count(),
                'completed' => $this->getCompletedRequests()->count(),
                'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
                'rejected_total' => $this->getRequestsWithRejections()->count(),
                
                // Overall counts
                'total' => (clone $baseQuery)->count(),
                'in_progress' => $this->getRequestsInProgress()->count(),
                
                // Time-based statistics
                'thisMonth' => (clone $baseQuery)->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                'lastMonth' => (clone $baseQuery)->whereMonth('created_at', Carbon::now()->subMonth()->month)
                    ->whereYear('created_at', Carbon::now()->subMonth()->year)
                    ->count(),
                    
                // Additional workflow insights
                'workflow_stages' => [
                    'pending_hod' => $this->getPendingRequestsForStage('hod')->count(),
                    'pending_divisional' => $this->getPendingRequestsForStage('divisional')->count(),
                    'pending_ict_director' => $this->getPendingRequestsForStage('ict_director')->count(),
                    'pending_head_it' => $this->getPendingRequestsForStage('head_it')->count(),
                    'pending_ict_officer' => $this->getPendingRequestsForStage('ict_officer')->count(),
                ],
                
                'departments' => Department::pluck('name')->toArray()
            ];

            Log::info('ICT Director Combined Access: Statistics retrieved successfully', $stats);

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('ICT Director Combined Access: Error fetching statistics', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'data' => [
                    'pending' => 0,
                    'hodApproved' => 0,
                    'divisionalApproved' => 0,
                    'pendingDict' => 0,
                    'dictApproved' => 0,
                    'dictRejected' => 0,
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
            
            // Include module data for conditional display
            'wellsoft_modules' => $request->wellsoft_modules,
            'jeeva_modules' => $request->jeeva_modules,
            'internet_purposes' => $request->internet_purposes,
            'access_type' => $request->access_type,
            'temporary_until' => $request->temporary_until?->format('Y-m-d'),
            
            'status' => $this->mapStatusForICTDirector($request->ict_director_status ?? $request->status),
            'dict_status' => $request->ict_director_status, // Direct access to new status column
            'status_display' => $this->getStatusDisplayName($request->ict_director_status ?? $request->status),
            'raw_status' => $request->status, // Keep legacy status for debugging
            
            // New granular status columns
            'hod_status' => $request->hod_status,
            'divisional_status' => $request->divisional_status,
            'ict_director_status' => $request->ict_director_status,
            'head_it_status' => $request->head_it_status,
            'ict_officer_status' => $request->ict_officer_status,
            'signature_path' => $request->signature_path,
            'created_at' => $request->created_at,
            'updated_at' => $request->updated_at,
            'submission_date' => $request->created_at,
            
            // HOD approval info (for context)
            'hod_comments' => $request->hod_comments ?? '',
            'hod_name' => $request->hod_name ?? '',
            'hod_approved_at' => $request->hod_approved_at,
            'hod_approval_date' => $request->hod_approved_at, // Alias for frontend
            
            // Divisional approval info (for context)
            'divisional_comments' => $request->divisional_comments ?? '',
            'divisional_name' => $request->divisional_name ?? '',
            'divisional_approved_at' => $request->divisional_approved_at,
            
            // ICT Director approval info
            'dict_comments' => $request->dict_comments ?? '',
            'ict_director_comments' => $request->ict_director_comments ?? '',
            'ict_director_rejection_reasons' => $request->ict_director_rejection_reasons ?? '',
            'dict_name' => $request->dict_name ?? '',
            'dict_approved_at' => $request->dict_approved_at,
            
            // Approval workflow status
            'hod_approval_status' => $this->getApprovalStatus($request, 'hod'),
            'divisional_approval_status' => $this->getApprovalStatus($request, 'divisional'),
            'dict_approval_status' => $this->getApprovalStatus($request, 'dict'),
            'head_it_approval_status' => $this->getApprovalStatus($request, 'head_it'),
            'ict_approval_status' => $this->getApprovalStatus($request, 'ict'),
        ];
    }

    /**
     * Map database status to ICT Director perspective using new granular status columns
     */
    private function mapStatusForICTDirector($ictDirectorStatus): string
    {
        // Map the granular ICT Director status to frontend-friendly values
        switch ($ictDirectorStatus) {
            case 'pending':
                return 'pending'; // Pending ICT Director approval
            case 'approved':
                return 'approved'; // Approved by ICT Director
            case 'rejected':
                return 'rejected'; // Rejected by ICT Director
            default:
                return $ictDirectorStatus ?: 'pending'; // Default to pending if null
        }
    }
    
    /**
     * Get user-friendly status display name using new granular status columns
     */
    private function getStatusDisplayName($ictDirectorStatus): string
    {
        switch ($ictDirectorStatus) {
            case 'pending':
                return 'Pending ICT Director Approval';
            case 'approved':
                return 'Approved by ICT Director';
            case 'rejected':
                return 'Rejected by ICT Director';
            case 'implemented':
                return 'Implemented';
            case 'cancelled':
                return 'Cancelled';
            default:
                return ucwords(str_replace('_', ' ', $ictDirectorStatus ?: 'pending'));
        }
    }

    /**
     * Get approval status for a specific role using new granular status columns
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
