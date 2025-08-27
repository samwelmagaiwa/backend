<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DepartmentHodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('admin');
    }

    /**
     * Display departments with their HOD assignments.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Department::with(['headOfDepartment.roles']);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by HOD assignment status
            if ($request->has('hod_status')) {
                switch ($request->hod_status) {
                    case 'assigned':
                        $query->whereNotNull('hod_user_id');
                        break;
                    case 'unassigned':
                        $query->whereNull('hod_user_id');
                        break;
                }
            }

            // Filter by active status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            
            if (in_array($sortBy, ['name', 'code', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('name', 'asc');
            }

            // Include request counts for each department
            $query->withCount(['userAccessRequests as pending_requests_count' => function ($q) {
                $q->where('status', 'pending');
            }]);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $departments = $query->paginate($perPage);

            // Transform the data
            $departments->getCollection()->transform(function ($department) {
                return [
                    'id' => $department->id,
                    'name' => $department->name,
                    'code' => $department->code,
                    'description' => $department->description,
                    'is_active' => $department->is_active,
                    'level' => $department->level ?? 1,
                    'parent_department_id' => $department->parent_department_id,
                    'pending_requests_count' => $department->pending_requests_count,
                    'hod' => $department->headOfDepartment ? [
                        'id' => $department->headOfDepartment->id,
                        'name' => $department->headOfDepartment->name,
                        'email' => $department->headOfDepartment->email,
                        'pf_number' => $department->headOfDepartment->pf_number,
                        'roles' => $department->headOfDepartment->roles->pluck('name')->toArray(),
                    ] : null,
                    'created_at' => $department->created_at,
                    'updated_at' => $department->updated_at,
                ];
            });

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
     * Assign HOD to a department.
     */
    public function assignHod(Request $request, Department $department): JsonResponse
    {
        $request->validate([
            'hod_user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('departments', 'hod_user_id')->ignore($department->id),
            ]
        ], [
            'hod_user_id.required' => 'HOD user is required.',
            'hod_user_id.exists' => 'Selected user does not exist.',
            'hod_user_id.unique' => 'This user is already assigned as HOD to another department.',
        ]);

        DB::beginTransaction();
        
        try {
            $hodUser = User::findOrFail($request->hod_user_id);
            $currentUser = $request->user();

            // Check if user has appropriate role
            if (!$hodUser->hasAnyRole(['head_of_department', 'ict_director', 'admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user does not have appropriate role to be HOD.'
                ], 422);
            }

            $previousHodId = $department->hod_user_id;
            
            // Update department
            $department->update(['hod_user_id' => $hodUser->id]);

            // Log the change
            Log::info('HOD assigned to department', [
                'department_id' => $department->id,
                'department_name' => $department->name,
                'new_hod_id' => $hodUser->id,
                'new_hod_email' => $hodUser->email,
                'previous_hod_id' => $previousHodId,
                'assigned_by' => $currentUser->id
            ]);

            DB::commit();

            // Return updated department
            $department->load('headOfDepartment.roles');

            return response()->json([
                'success' => true,
                'data' => $department,
                'message' => "HOD assigned to {$department->name} successfully."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning HOD: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign HOD.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove HOD from a department.
     */
    public function removeHod(Request $request, Department $department): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            if (!$department->hod_user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Department does not have an assigned HOD.'
                ], 422);
            }

            $previousHodId = $department->hod_user_id;
            $previousHod = $department->headOfDepartment;
            
            // Remove HOD assignment
            $department->update(['hod_user_id' => null]);

            // Log the change
            Log::info('HOD removed from department', [
                'department_id' => $department->id,
                'department_name' => $department->name,
                'previous_hod_id' => $previousHodId,
                'previous_hod_email' => $previousHod?->email,
                'removed_by' => $request->user()->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "HOD removed from {$department->name} successfully."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing HOD: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove HOD.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get eligible HOD users.
     */
    public function eligibleHods(): JsonResponse
    {
        try {
            $eligibleUsers = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['head_of_department', 'ict_director', 'admin']);
            })
            ->with('roles', 'departmentsAsHOD')
            ->orderBy('name')
            ->get();

            $users = $eligibleUsers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'pf_number' => $user->pf_number,
                    'roles' => $user->roles->pluck('name')->toArray(),
                    'current_departments' => $user->departmentsAsHOD->map(function ($dept) {
                        return [
                            'id' => $dept->id,
                            'name' => $dept->name,
                            'code' => $dept->code,
                        ];
                    }),
                    'is_available' => $user->departmentsAsHOD->isEmpty(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Eligible HOD users retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving eligible HODs: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve eligible HODs.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get department HOD assignment statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_departments' => Department::count(),
                'departments_with_hod' => Department::whereNotNull('hod_user_id')->count(),
                'departments_without_hod' => Department::whereNull('hod_user_id')->count(),
                'active_departments' => Department::where('is_active', true)->count(),
                'inactive_departments' => Department::where('is_active', false)->count(),
                'departments_with_pending_requests' => Department::has('userAccessRequests', '>', 0)->count(),
                'hod_distribution' => User::has('departmentsAsHOD')
                    ->with('departmentsAsHOD', 'roles')
                    ->get()
                    ->map(function ($user) {
                        return [
                            'user_name' => $user->name,
                            'user_email' => $user->email,
                            'roles' => $user->roles->pluck('name')->toArray(),
                            'departments_count' => $user->departmentsAsHOD->count(),
                            'departments' => $user->departmentsAsHOD->map(function ($dept) {
                                return [
                                    'name' => $dept->name,
                                    'code' => $dept->code,
                                ];
                            }),
                        ];
                    }),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Department HOD statistics retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving department statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}