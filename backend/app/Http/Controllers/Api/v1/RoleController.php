<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use App\Models\RoleChangeLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('admin')->except(['index', 'show']);
    }

    /**
     * Display a listing of roles.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Role::query();

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by type
            if ($request->has('type')) {
                switch ($request->type) {
                    case 'system':
                        $query->systemRoles();
                        break;
                    case 'custom':
                        $query->customRoles();
                        break;
                    case 'deletable':
                        $query->deletable();
                        break;
                }
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'sort_order');
            $sortOrder = $request->get('sort_order', 'asc');
            
            if (in_array($sortBy, ['name', 'created_at', 'sort_order'])) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('sort_order', 'asc');
            }

            // Include user count
            $query->withCount('users');

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $roles = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $roles,
                'message' => 'Roles retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving roles: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve roles.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created role.
     */
    public function store(RoleRequest $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $role = Role::create($request->validated());

            // Log the creation
            Log::info('Role created', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'created_by' => $request->user()->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $role->load('users'),
                'message' => 'Role created successfully.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating role: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): JsonResponse
    {
        try {
            $role->load(['users' => function ($query) {
                $query->select('users.id', 'users.name', 'users.email')
                      ->withPivot(['assigned_at', 'assigned_by']);
            }]);

            return response()->json([
                'success' => true,
                'data' => $role,
                'message' => 'Role retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving role: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve role.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update the specified role.
     */
    public function update(RoleRequest $request, Role $role): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            // Store original data for logging
            $originalData = $role->toArray();
            
            $role->update($request->validated());

            // Log the update
            Log::info('Role updated', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'updated_by' => $request->user()->id,
                'changes' => array_diff_assoc($role->toArray(), $originalData)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $role->load('users'),
                'message' => 'Role updated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating role: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Request $request, Role $role): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            // Check if role can be deleted
            if (!$role->canBeDeleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This role cannot be deleted as it is a system role.'
                ], 422);
            }

            // Check if role has users
            $userCount = $role->users()->count();
            if ($userCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete role. It is assigned to {$userCount} user(s)."
                ], 422);
            }

            $roleName = $role->name;
            $roleId = $role->id;

            $role->delete();

            // Log the deletion
            Log::info('Role deleted', [
                'role_id' => $roleId,
                'role_name' => $roleName,
                'deleted_by' => $request->user()->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting role: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get role statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_roles' => Role::count(),
                'system_roles' => Role::systemRoles()->count(),
                'custom_roles' => Role::customRoles()->count(),
                'deletable_roles' => Role::deletable()->count(),
                'roles_with_users' => Role::has('users')->count(),
                'recent_changes' => RoleChangeLog::recent(7)->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Role statistics retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving role statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve role statistics.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get available permissions
     */
    public function permissions(): JsonResponse
    {
        try {
            $permissions = [
                'user_management' => [
                    'create_users',
                    'edit_users',
                    'delete_users',
                    'view_users'
                ],
                'role_management' => [
                    'create_roles',
                    'edit_roles',
                    'delete_roles',
                    'assign_roles'
                ],
                'department_management' => [
                    'create_departments',
                    'edit_departments',
                    'delete_departments',
                    'assign_hod'
                ],
                'request_management' => [
                    'view_all_requests',
                    'approve_requests',
                    'reject_requests',
                    'export_requests'
                ],
                'system_administration' => [
                    'system_settings',
                    'audit_logs',
                    'backup_restore',
                    'maintenance_mode'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $permissions,
                'message' => 'Available permissions retrieved successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving permissions: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve permissions.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}