<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\ModuleAccessRequest;
use App\Models\User;
use App\Models\Department;
use App\Models\UserAccess;
use App\Http\Requests\BothServiceForm\CreateBothServiceFormRequest;
use App\Http\Requests\BothServiceForm\ApprovalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PDF;

class BothServiceFormController extends Controller
{
    /**
     * Get user information for auto-population from user_access table
     */
    public function getUserInfo(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Get latest user access request for department info
            $latestAccess = UserAccess::where('user_id', $user->id)
                ->with('department')
                ->latest()
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'pf_number' => $user->pf_number ?? '',
                    'staff_name' => $user->name ?? '',
                    'phone_number' => $user->phone ?? '',
                    'department_id' => $latestAccess?->department_id,
                    'department_name' => $latestAccess?->department?->name,
                    'role' => $user->role?->name ?? '',
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting user info for both-service-form', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get user information'
            ], 500);
        }
    }

    /**
     * Get personal information from user_access table for HOD dashboard
     */
    public function getPersonalInfoFromUserAccess(Request $request, int $userAccessId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            $userRole = $currentUser->getPrimaryRole()?->name; // For backward compatibility

            // Check if user has permission to access this data
            $allowedRoles = ['head_of_department', 'divisional_director', 'ict_director', 'ict_officer', 'admin', 'super_admin'];
            if (!array_intersect($userRoles, $allowedRoles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Insufficient permissions.'
                ], 403);
            }

            // Get user access record with related data
            $userAccess = UserAccess::with(['user', 'department'])
                ->findOrFail($userAccessId);

            // For HOD, ensure they can only access requests from their department
            if (array_intersect($userRoles, ['head_of_department'])) {
                $hodDepartment = Department::where('hod_user_id', $currentUser->id)->first();
                if (!$hodDepartment || $userAccess->department_id !== $hodDepartment->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied. You can only access requests from your department.'
                    ], 403);
                }
            }

            // Prepare personal information data
            $personalInfo = [
                'pf_number' => $userAccess->pf_number,
                'staff_name' => $userAccess->staff_name,
                'department' => $userAccess->department?->name ?? 'N/A',
                'department_id' => $userAccess->department_id,
                'contact_number' => $userAccess->phone_number,
                'signature' => [
                    'path' => $userAccess->signature_path,
                    'url' => $userAccess->signature_path ? Storage::url($userAccess->signature_path) : null,
                    'exists' => $userAccess->signature_path ? Storage::exists($userAccess->signature_path) : false
                ],
                'request_details' => [
                    'id' => $userAccess->id,
                    'request_type' => $userAccess->request_type,
                    'purpose' => $userAccess->purpose,
                    'status' => $userAccess->status,
                    'created_at' => $userAccess->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $userAccess->updated_at->format('Y-m-d H:i:s')
                ],
                'user_details' => [
                    'id' => $userAccess->user->id,
                    'email' => $userAccess->user->email,
                    'role' => $userAccess->user->role?->name ?? 'N/A'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $personalInfo,
                'meta' => [
                    'accessed_by' => $currentUser->name,
                    'accessed_by_role' => $userRole,
                    'access_time' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting personal info from user_access', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId,
                'current_user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get personal information'
            ], 500);
        }
    }

    /**
     * Get all user access requests for approval dashboard with personal information
     * Supports HOD and Divisional Director roles
     */
    public function getUserAccessRequestsForHOD(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            $userRole = $currentUser->getPrimaryRole()?->name; // For backward compatibility

            // Check if user has approval permissions
            if (!array_intersect($userRoles, ['head_of_department', 'divisional_director'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only HOD and Divisional Director can access this endpoint.'
                ], 403);
            }

            // Get user's department based on role
            Log::info('Department Lookup Debug', [
                'current_user_id' => $currentUser->id,
                'current_user_email' => $currentUser->email,
                'current_user_roles' => $userRoles,
                'primary_role' => $userRole,
            ]);
            
            $userDepartment = null;
            
            // For HOD, find department where they are assigned as HOD
            if (array_intersect($userRoles, ['head_of_department'])) {
                $userDepartment = Department::where('hod_user_id', $currentUser->id)->first();
            }
            
            // For Divisional Director, find department where they are assigned as divisional director
            if (array_intersect($userRoles, ['divisional_director']) && !$userDepartment) {
                $userDepartment = Department::where('divisional_director_id', $currentUser->id)->first();
            }
            
            // Debug: Log all departments and their assignments
            $allDepartments = Department::select('id', 'name', 'code', 'hod_user_id', 'divisional_director_id')->get();
            Log::info('All Departments Debug', [
                'departments' => $allDepartments->toArray()
            ]);
            
            if (!$userDepartment) {
                Log::error('No department found for user', [
                    'user_id' => $currentUser->id,
                    'user_email' => $currentUser->email,
                    'user_roles' => $userRoles,
                    'all_departments' => $allDepartments->toArray()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'No department assigned to user.',
                    'debug' => [
                        'user_id' => $currentUser->id,
                        'user_email' => $currentUser->email,
                        'user_roles' => $userRoles,
                        'departments_count' => $allDepartments->count(),
                        'departments_with_hod' => $allDepartments->whereNotNull('hod_user_id')->values(),
                        'departments_with_divisional_director' => $allDepartments->whereNotNull('divisional_director_id')->values()
                    ]
                ], 404);
            }

            // Get user access requests from user's department
            $userAccessRequests = UserAccess::with(['user', 'department'])
                ->where('department_id', $userDepartment->id)
                ->where('status', 'pending') // Only pending requests for approval
                ->orderBy('created_at', 'asc') // FIFO order
                ->get();

            // Transform data for approval dashboard
            $requestsData = $userAccessRequests->map(function ($userAccess) {
                return [
                    'id' => $userAccess->id,
                    'personal_information' => [
                        'pf_number' => $userAccess->pf_number,
                        'staff_name' => $userAccess->staff_name,
                        'department' => $userAccess->department?->name ?? 'N/A',
                        'contact_number' => $userAccess->phone_number,
                        'signature' => [
                            'exists' => $userAccess->signature_path ? true : false,
                            'url' => $userAccess->signature_path ? Storage::url($userAccess->signature_path) : null,
                            'status' => $userAccess->signature_path ? 'Uploaded' : 'No signature uploaded'
                        ]
                    ],
                    'request_details' => [
                        'request_type' => $userAccess->request_type,
                        'purpose' => $userAccess->purpose,
                        'status' => $userAccess->status,
                        'submission_date' => $userAccess->created_at->format('Y-m-d'),
                        'submission_time' => $userAccess->created_at->format('H:i:s'),
                        'days_pending' => $userAccess->created_at->diffInDays(now())
                    ],
                    'user_info' => [
                        'email' => $userAccess->user->email,
                        'role' => $userAccess->user->role?->name ?? 'N/A'
                    ],
                    'actions' => [
                        'can_view' => true,
                        'can_approve' => true,
                        'can_reject' => true,
                        'can_edit_comments' => true
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $requestsData,
                'meta' => [
                    'total_requests' => $requestsData->count(),
                    'department' => $userDepartment->name,
                    'approver_name' => $currentUser->name,
                    'approver_role' => $userRole,
                    'last_updated' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting user access requests for HOD', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get user access requests'
            ], 500);
        }
    }

    /**
     * Get departments list
     */
    public function getDepartments(): JsonResponse
    {
        try {
            $departments = Department::active()
                ->select('id', 'name', 'code')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $departments
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting departments', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get departments'
            ], 500);
        }
    }
}