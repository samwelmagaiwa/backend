<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\UserAccess;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugHodRecommendationsController extends Controller
{
    /**
     * Debug HOD divisional recommendations - step by step analysis
     */
    public function debugHodRecommendations(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            $debug = [
                'step_1_user_info' => [
                    'user_id' => $currentUser->id,
                    'user_name' => $currentUser->name,
                    'user_email' => $currentUser->email,
                    'roles' => $currentUser->roles()->pluck('name')->toArray()
                ]
            ];
            
            // Step 2: Check HOD departments
            $hodDepartments = Department::where('hod_user_id', $currentUser->id)->get();
            $debug['step_2_hod_departments'] = [
                'count' => $hodDepartments->count(),
                'departments' => $hodDepartments->map(function($dept) {
                    return [
                        'id' => $dept->id,
                        'name' => $dept->name,
                        'code' => $dept->code ?? 'N/A'
                    ];
                })->toArray()
            ];
            
            if ($hodDepartments->isEmpty()) {
                $debug['conclusion'] = 'ISSUE: HOD has no departments assigned';
                return response()->json($debug);
            }
            
            $hodDepartmentIds = $hodDepartments->pluck('id')->toArray();
            
            // Step 3: Check all requests in HOD departments
            $allRequestsInDept = UserAccess::whereIn('department_id', $hodDepartmentIds)->get();
            $debug['step_3_all_requests_in_dept'] = [
                'count' => $allRequestsInDept->count(),
                'sample_requests' => $allRequestsInDept->take(5)->map(function($req) {
                    return [
                        'id' => $req->id,
                        'staff_name' => $req->staff_name,
                        'status' => $req->status,
                        'has_divisional_comments' => !empty($req->divisional_director_comments),
                        'divisional_comments_preview' => substr($req->divisional_director_comments ?? '', 0, 50) . '...'
                    ];
                })->toArray()
            ];
            
            // Step 4: Check requests with divisional comments
            $requestsWithComments = UserAccess::whereIn('department_id', $hodDepartmentIds)
                ->whereNotNull('divisional_director_comments')
                ->where('divisional_director_comments', '!=', '')
                ->get();
                
            $debug['step_4_requests_with_comments'] = [
                'count' => $requestsWithComments->count(),
                'sample_requests' => $requestsWithComments->take(5)->map(function($req) {
                    return [
                        'id' => $req->id,
                        'staff_name' => $req->staff_name,
                        'status' => $req->status,
                        'divisional_comments_length' => strlen($req->divisional_director_comments ?? ''),
                        'divisional_approved_at' => $req->divisional_approved_at
                    ];
                })->toArray()
            ];
            
            // Step 5: Check status filtering
            $allowedStatuses = [
                'divisional_approved',
                'divisional_rejected', 
                'pending_ict_director',
                'dict_approved',
                'dict_rejected',
                'rejected'
            ];
            
            $finalQuery = UserAccess::whereIn('department_id', $hodDepartmentIds)
                ->whereNotNull('divisional_director_comments')
                ->where('divisional_director_comments', '!=', '')
                ->whereIn('status', $allowedStatuses)
                ->get();
                
            $debug['step_5_final_filtered_requests'] = [
                'allowed_statuses' => $allowedStatuses,
                'count' => $finalQuery->count(),
                'requests' => $finalQuery->map(function($req) {
                    return [
                        'id' => $req->id,
                        'staff_name' => $req->staff_name,
                        'status' => $req->status,
                        'department_id' => $req->department_id,
                        'divisional_comments' => substr($req->divisional_director_comments, 0, 100) . '...',
                        'divisional_approved_at' => $req->divisional_approved_at,
                        'created_at' => $req->created_at,
                        'updated_at' => $req->updated_at
                    ];
                })->toArray()
            ];
            
            // Step 6: Check all possible statuses in the department
            $statusBreakdown = UserAccess::whereIn('department_id', $hodDepartmentIds)
                ->whereNotNull('divisional_director_comments')
                ->where('divisional_director_comments', '!=', '')
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();
                
            $debug['step_6_status_breakdown'] = [
                'all_statuses_with_comments' => $statusBreakdown->map(function($item) {
                    return [
                        'status' => $item->status,
                        'count' => $item->count,
                        'is_allowed' => in_array($item->status, $allowedStatuses)
                    ];
                })->toArray()
            ];
            
            // Conclusion
            if ($finalQuery->count() > 0) {
                $debug['conclusion'] = 'SUCCESS: Found recommendations, check frontend filtering or API call';
            } else if ($requestsWithComments->count() > 0) {
                $debug['conclusion'] = 'ISSUE: Requests have comments but wrong statuses for HOD view';
            } else if ($allRequestsInDept->count() > 0) {
                $debug['conclusion'] = 'ISSUE: Requests exist in department but no divisional comments';
            } else {
                $debug['conclusion'] = 'ISSUE: No requests at all in HOD departments';
            }
            
            return response()->json($debug);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
