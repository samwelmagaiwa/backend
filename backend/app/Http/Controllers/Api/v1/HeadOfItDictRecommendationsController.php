<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\UserAccess;
use App\Services\StatusMigrationService;
use App\Traits\HandlesStatusQueries;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HeadOfItDictRecommendationsController extends Controller
{
    use HandlesStatusQueries;
    
    protected StatusMigrationService $statusMigrationService;
    
    public function __construct(StatusMigrationService $statusMigrationService)
    {
        $this->statusMigrationService = $statusMigrationService;
    }
    
    /**
     * Get DICT recommendations for Head of IT with search and filter support
     * Shows requests that have ICT Director comments (rejections, approvals, recommendations)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getDictRecommendations(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Validate query parameters
            $validator = Validator::make($request->all(), [
                'search' => 'nullable|string|max:255',
                'status' => 'nullable|string|in:ict_director_approved,dict_approved,ict_director_rejected,dict_rejected,pending_ict_director,implemented,pending_head_it,head_it_approved',
                'request_type' => 'nullable|string|in:jeeva_access,wellsoft,internet_access_request',
                'has_comments' => 'nullable|boolean',
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Build the base query using Eloquent with new granular status system
            // Head of IT sees all requests across departments (no department filter)
            $query = UserAccess::with(['user', 'department'])
                ->where(function ($q) {
                    // Only get requests with ICT Director comments (recommendations/rejections/approvals)
                    $q->whereNotNull('ict_director_comments')
                      ->where('ict_director_comments', '!=', '');
                })
                // Show requests that are either approved by ICT Director or have been processed by Head IT
                ->where(function ($q) {
                    $q->where('ict_director_status', 'approved')
                      ->orWhere('head_it_status', 'approved')
                      ->orWhere('head_it_status', 'rejected');
                });
                
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('staff_name', 'LIKE', "%{$search}%")
                      ->orWhere('pf_number', 'LIKE', "%{$search}%")
                      ->orWhere('id', 'LIKE', "%{$search}%")
                      ->orWhereRaw('CONCAT("REQ-", LPAD(id, 6, "0")) LIKE ?', ["%{$search}%"]);
                });
            }

            // Apply status filter - use granular status system
            if ($request->filled('status')) {
                $status = $request->input('status');
                // Map to new granular status columns
                switch ($status) {
                    case 'ict_director_approved':
                    case 'dict_approved':
                        $query->where('ict_director_status', 'approved');
                        break;
                    case 'ict_director_rejected':
                    case 'dict_rejected':
                        $query->where('ict_director_status', 'rejected');
                        break;
                    case 'pending_ict_director':
                        $query->where('ict_director_status', 'pending');
                        break;
                    case 'pending_head_it':
                        $query->where('ict_director_status', 'approved')
                              ->where(function($q) {
                                  $q->whereNull('head_it_status')
                                    ->orWhere('head_it_status', 'pending');
                              });
                        break;
                    case 'head_it_approved':
                        $query->where('head_it_status', 'approved');
                        break;
                    case 'implemented':
                        $query->where('ict_officer_status', 'implemented');
                        break;
                    default:
                        // Fallback to legacy status for backward compatibility
                        $query->where('status', $status);
                        break;
                }
            }

            // Apply request type filter
            if ($request->filled('request_type')) {
                $requestType = $request->input('request_type');
                $query->where(function ($q) use ($requestType) {
                    $q->whereRaw('JSON_CONTAINS(request_type, ?)', [json_encode($requestType)])
                      ->orWhere('request_type', 'LIKE', "%{$requestType}%");
                });
            }

            // Apply comments filter
            if ($request->has('has_comments')) {
                if ($request->boolean('has_comments')) {
                    $query->whereNotNull('ict_director_comments')
                          ->where('ict_director_comments', '!=', '');
                } else {
                    $query->where(function ($q) {
                        $q->whereNull('ict_director_comments')
                          ->orWhere('ict_director_comments', '=', '');
                    });
                }
            }

            // Get pagination parameters
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            $offset = ($page - 1) * $perPage;

            // Get total count for pagination
            $total = $query->count();

            // Apply pagination and ordering (newest first)
            $recommendations = $query
                ->orderByRaw('COALESCE(ict_director_approved_at, "1970-01-01") DESC') // MySQL equivalent of NULLS LAST
                ->orderBy('updated_at', 'desc')
                ->limit($perPage)
                ->offset($offset)
                ->get()
                ->map(function ($item) {
                    // Decode JSON fields
                    if ($item->request_type) {
                        $item->request_type = is_string($item->request_type) 
                            ? json_decode($item->request_type, true) ?? [$item->request_type]
                            : $item->request_type;
                    }
                    
                    if ($item->wellsoft_modules_selected && is_string($item->wellsoft_modules_selected)) {
                        $item->wellsoft_modules_selected = json_decode($item->wellsoft_modules_selected, true) ?? [];
                    }
                    
                    if ($item->jeeva_modules_selected && is_string($item->jeeva_modules_selected)) {
                        $item->jeeva_modules_selected = json_decode($item->jeeva_modules_selected, true) ?? [];
                    }
                    
                    if ($item->internet_purposes && is_string($item->internet_purposes)) {
                        $item->internet_purposes = json_decode($item->internet_purposes, true) ?? [];
                    }
                    
                    // Add granular status information
                    $item->workflow_status = [
                        'hod_status' => $item->hod_status,
                        'divisional_status' => $item->divisional_status,
                        'ict_director_status' => $item->ict_director_status,
                        'head_it_status' => $item->head_it_status,
                        'ict_officer_status' => $item->ict_officer_status
                    ];
                    
                    // Determine current stage
                    $item->current_stage = $this->determineCurrentStage($item);
                    
                    // Add ICT Director recommendation info
                    $item->ict_director_info = [
                        'status' => $item->ict_director_status,
                        'comments' => $item->ict_director_comments,
                        'rejection_reasons' => $item->ict_director_rejection_reasons,
                        'approved_by' => $item->ict_director_name,
                        'approved_at' => $item->ict_director_approved_at,
                    ];

                    // Add Head of IT info if available
                    $item->head_it_info = [
                        'status' => $item->head_it_status,
                        'comments' => $item->head_it_comments,
                        'approved_by' => $item->head_it_name,
                        'approved_at' => $item->head_it_approved_at,
                    ];

                    // Format request ID
                    $item->request_id_formatted = 'REQ-' . str_pad($item->id, 6, '0', STR_PAD_LEFT);
                    
                    return $item;
                });

            // Calculate pagination info
            $lastPage = ceil($total / $perPage);

            return response()->json([
                'success' => true,
                'data' => $recommendations,
                'pagination' => [
                    'current_page' => $page,
                    'last_page' => $lastPage,
                    'per_page' => $perPage,
                    'total' => $total,
                    'from' => $offset + 1,
                    'to' => min($offset + $perPage, $total)
                ],
                'filters_applied' => [
                    'search' => $request->input('search'),
                    'status' => $request->input('status'),
                    'request_type' => $request->input('request_type'),
                    'has_comments' => $request->input('has_comments')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching DICT recommendations for Head of IT', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch DICT recommendations',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get statistics for DICT recommendations
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getRecommendationStats(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $stats = DB::table('user_access')
                ->whereNotNull('ict_director_comments')
                ->where('ict_director_comments', '!=', '')
                ->where(function ($q) {
                    // Show requests that are either approved by ICT Director or have been processed by Head IT
                    $q->where('ict_director_status', 'approved')
                      ->orWhere('head_it_status', 'approved')
                      ->orWhere('head_it_status', 'rejected');
                })
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN ict_director_status = "approved" THEN 1 ELSE 0 END) as dict_approved,
                    SUM(CASE WHEN ict_director_status = "rejected" THEN 1 ELSE 0 END) as dict_rejected,
                    SUM(CASE WHEN head_it_status = "approved" THEN 1 ELSE 0 END) as head_it_approved,
                    SUM(CASE WHEN head_it_status = "rejected" THEN 1 ELSE 0 END) as head_it_rejected,
                    SUM(CASE WHEN ict_director_status = "approved" AND (head_it_status IS NULL OR head_it_status = "pending") THEN 1 ELSE 0 END) as pending_head_it,
                    SUM(CASE WHEN ict_officer_status = "implemented" THEN 1 ELSE 0 END) as implemented
                ')
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => (int)$stats->total,
                    'dict_approved' => (int)$stats->dict_approved,
                    'dict_rejected' => (int)$stats->dict_rejected,
                    'head_it_approved' => (int)$stats->head_it_approved,
                    'head_it_rejected' => (int)$stats->head_it_rejected,
                    'pending_head_it' => (int)$stats->pending_head_it,
                    'implemented' => (int)$stats->implemented
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching DICT recommendations stats for Head of IT', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get detailed information for a specific request
     * 
     * @param Request $request
     * @param int $userAccessId
     * @return JsonResponse
     */
    public function getRequestDetails(Request $request, int $userAccessId): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $requestDetails = DB::table('user_access')
                ->leftJoin('departments', 'user_access.department_id', '=', 'departments.id')
                ->leftJoin('users', 'user_access.user_id', '=', 'users.id')
                ->where('user_access.id', $userAccessId)
                ->whereNotNull('ict_director_comments')
                ->where('ict_director_comments', '!=', '')
                ->select(
                    'user_access.*',
                    'departments.name as department_name',
                    'users.email as user_email'
                )
                ->first();

            if (!$requestDetails) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found or access denied'
                ], 404);
            }

            // Decode JSON fields
            if ($requestDetails->request_type) {
                $requestDetails->request_type = json_decode($requestDetails->request_type, true) ?? [$requestDetails->request_type];
            }
            
            if ($requestDetails->wellsoft_modules_selected) {
                $requestDetails->wellsoft_modules_selected = json_decode($requestDetails->wellsoft_modules_selected, true) ?? [];
            }
            
            if ($requestDetails->jeeva_modules_selected) {
                $requestDetails->jeeva_modules_selected = json_decode($requestDetails->jeeva_modules_selected, true) ?? [];
            }
            
            if ($requestDetails->internet_purposes) {
                $requestDetails->internet_purposes = json_decode($requestDetails->internet_purposes, true) ?? [];
            }

            // Format request ID
            $requestDetails->request_id_formatted = 'REQ-' . str_pad($requestDetails->id, 6, '0', STR_PAD_LEFT);

            // Add workflow status
            $requestDetails->workflow_status = [
                'hod_status' => $requestDetails->hod_status,
                'divisional_status' => $requestDetails->divisional_status,
                'ict_director_status' => $requestDetails->ict_director_status,
                'head_it_status' => $requestDetails->head_it_status,
                'ict_officer_status' => $requestDetails->ict_officer_status
            ];

            return response()->json([
                'success' => true,
                'data' => $requestDetails
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching request details for Head of IT', [
                'user_id' => Auth::id(),
                'request_id' => $userAccessId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch request details',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    
    /**
     * Determine the current workflow stage based on granular status columns
     */
    private function determineCurrentStage($userAccess): string
    {
        if ($userAccess->ict_officer_status === 'implemented') {
            return 'completed';
        }
        
        if ($userAccess->hod_status === 'rejected' || 
            $userAccess->divisional_status === 'rejected' ||
            $userAccess->ict_director_status === 'rejected' ||
            $userAccess->head_it_status === 'rejected' ||
            $userAccess->ict_officer_status === 'rejected') {
            return 'rejected';
        }
        
        if ($userAccess->head_it_status === 'approved' && ($userAccess->ict_officer_status === 'pending' || is_null($userAccess->ict_officer_status))) {
            return 'pending_ict_officer';
        }
        
        if ($userAccess->ict_director_status === 'approved' && ($userAccess->head_it_status === 'pending' || is_null($userAccess->head_it_status))) {
            return 'pending_head_it';
        }
        
        if ($userAccess->divisional_status === 'approved' && ($userAccess->ict_director_status === 'pending' || is_null($userAccess->ict_director_status))) {
            return 'pending_ict_director';
        }
        
        if ($userAccess->hod_status === 'approved' && ($userAccess->divisional_status === 'pending' || is_null($userAccess->divisional_status))) {
            return 'pending_divisional';
        }
        
        if ($userAccess->hod_status === 'pending' || is_null($userAccess->hod_status)) {
            return 'pending_hod';
        }
        
        return 'unknown';
    }
}
