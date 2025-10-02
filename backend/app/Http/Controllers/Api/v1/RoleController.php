<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('admin');
    }

    /**
     * Display a listing of all roles.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Role::withCount('users');

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by system roles
            if ($request->has('system_only') && $request->boolean('system_only')) {
                $query->systemRoles();
            } elseif ($request->has('custom_only') && $request->boolean('custom_only')) {
                $query->customRoles();
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'sort_order');
            $sortOrder = $request->get('sort_order', 'asc');
            
            if (in_array($sortBy, ['name', 'created_at', 'sort_order'])) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('sort_order', 'asc');
            }

            // Get all roles (with user counts)
            $roles = $query->get();

            // Transform the data
            $roles->transform(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->getDisplayName(),
                    'description' => $role->description,
                    'permissions' => $role->permissions ?? [],
                    'users_count' => $role->users_count,
                    'is_system_role' => $role->is_system_role,
                    'is_deletable' => $role->is_deletable,
                    'sort_order' => $role->sort_order,
                    'created_at' => $role->created_at,
                    'updated_at' => $role->updated_at,
                ];
            });

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
     * Store a newly created role in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:roles,name|regex:/^[a-z_]+$/',
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'permissions' => 'nullable|array',
                'permissions.*' => 'integer|min:1|max:32', // Validate permission IDs against available permissions
                'is_system_role' => 'boolean',
                'is_deletable' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            DB::beginTransaction();
            
            // Create the role
            $role = Role::create([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? null,
                'permissions' => $this->convertPermissionIds($validated['permissions'] ?? []),
                'is_system_role' => $validated['is_system_role'] ?? false,
                'is_deletable' => $validated['is_deletable'] ?? true,
                'sort_order' => $validated['sort_order'] ?? null,
            ]);

            // Log role creation
            Log::info('New role created', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'permissions' => $role->permissions,
                'created_by' => $request->user()->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                    'description' => $role->description,
                    'permissions' => $role->permissions,
                    'is_system_role' => $role->is_system_role,
                    'is_deletable' => $role->is_deletable,
                    'sort_order' => $role->sort_order,
                    'created_at' => $role->created_at,
                ],
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
            $role->load('users');

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->getDisplayName(),
                    'description' => $role->description,
                    'permissions' => $role->permissions ?? [],
                    'users_count' => $role->users()->count(),
                    'users' => $role->users->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'assigned_at' => $user->pivot->assigned_at,
                        ];
                    }),
                    'is_system_role' => $role->is_system_role,
                    'is_deletable' => $role->is_deletable,
                    'sort_order' => $role->sort_order,
                    'created_at' => $role->created_at,
                    'updated_at' => $role->updated_at,
                ],
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
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        try {
            // Check if role can be modified
            if ($role->is_system_role && !$request->user()->hasRole('super_admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'System roles cannot be modified.'
                ], 422);
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-z_]+$/',
                    Rule::unique('roles', 'name')->ignore($role->id)
                ],
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'permissions' => 'nullable|array',
                'permissions.*' => 'integer|min:1|max:32', // Validate permission IDs against available permissions
                'is_deletable' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            DB::beginTransaction();

            // Update the role
            $role->update([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? $role->description,
                'permissions' => $this->convertPermissionIds($validated['permissions'] ?? []),
                'is_deletable' => $validated['is_deletable'] ?? $role->is_deletable,
                'sort_order' => $validated['sort_order'] ?? $role->sort_order,
            ]);

            // Log role update
            Log::info('Role updated', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'permissions' => $role->permissions,
                'updated_by' => $request->user()->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->getDisplayName(),
                    'description' => $role->description,
                    'permissions' => $role->permissions,
                    'is_system_role' => $role->is_system_role,
                    'is_deletable' => $role->is_deletable,
                    'sort_order' => $role->sort_order,
                    'updated_at' => $role->updated_at,
                ],
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
     * Remove the specified role from storage.
     */
    public function destroy(Role $role): JsonResponse
    {
        try {
            // Check if role can be deleted
            if (!$role->canBeDeleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This role cannot be deleted because it is a system role.'
                ], 422);
            }

            // Check if role is assigned to users
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role that is assigned to users. Please remove all users from this role first.'
                ], 422);
            }

            DB::beginTransaction();

            $roleName = $role->name;
            $role->delete();

            // Log role deletion
            Log::info('Role deleted', [
                'role_name' => $roleName,
                'deleted_by' => request()->user()->id
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
     * Get all available permissions for role creation/editing.
     */
    public function getPermissions(): JsonResponse
    {
        try {
            // Define available permissions - since we're using JSON array storage
            $permissions = [
                // User Management
                ['id' => 1, 'name' => 'users.view', 'display_name' => 'View Users', 'category' => 'users'],
                ['id' => 2, 'name' => 'users.create', 'display_name' => 'Create Users', 'category' => 'users'],
                ['id' => 3, 'name' => 'users.edit', 'display_name' => 'Edit Users', 'category' => 'users'],
                ['id' => 4, 'name' => 'users.delete', 'display_name' => 'Delete Users', 'category' => 'users'],
                ['id' => 5, 'name' => 'users.assign_roles', 'display_name' => 'Assign User Roles', 'category' => 'users'],

                // Role Management
                ['id' => 6, 'name' => 'roles.view', 'display_name' => 'View Roles', 'category' => 'roles'],
                ['id' => 7, 'name' => 'roles.create', 'display_name' => 'Create Roles', 'category' => 'roles'],
                ['id' => 8, 'name' => 'roles.edit', 'display_name' => 'Edit Roles', 'category' => 'roles'],
                ['id' => 9, 'name' => 'roles.delete', 'display_name' => 'Delete Roles', 'category' => 'roles'],

                // Department Management
                ['id' => 10, 'name' => 'departments.view', 'display_name' => 'View Departments', 'category' => 'departments'],
                ['id' => 11, 'name' => 'departments.create', 'display_name' => 'Create Departments', 'category' => 'departments'],
                ['id' => 12, 'name' => 'departments.edit', 'display_name' => 'Edit Departments', 'category' => 'departments'],
                ['id' => 13, 'name' => 'departments.delete', 'display_name' => 'Delete Departments', 'category' => 'departments'],
                ['id' => 14, 'name' => 'departments.assign_hod', 'display_name' => 'Assign Department HOD', 'category' => 'departments'],

                // Access Request Management
                ['id' => 15, 'name' => 'requests.view_all', 'display_name' => 'View All Requests', 'category' => 'requests'],
                ['id' => 16, 'name' => 'requests.approve', 'display_name' => 'Approve Requests', 'category' => 'requests'],
                ['id' => 17, 'name' => 'requests.reject', 'display_name' => 'Reject Requests', 'category' => 'requests'],
                ['id' => 18, 'name' => 'requests.cancel', 'display_name' => 'Cancel Requests', 'category' => 'requests'],

                // Device Management
                ['id' => 19, 'name' => 'devices.view', 'display_name' => 'View Device Inventory', 'category' => 'devices'],
                ['id' => 20, 'name' => 'devices.create', 'display_name' => 'Add Devices', 'category' => 'devices'],
                ['id' => 21, 'name' => 'devices.edit', 'display_name' => 'Edit Devices', 'category' => 'devices'],
                ['id' => 22, 'name' => 'devices.delete', 'display_name' => 'Delete Devices', 'category' => 'devices'],
                ['id' => 23, 'name' => 'devices.approve_booking', 'display_name' => 'Approve Device Bookings', 'category' => 'devices'],

                // System Administration
                ['id' => 24, 'name' => 'admin.full_access', 'display_name' => 'Full Admin Access', 'category' => 'admin'],
                ['id' => 25, 'name' => 'admin.view_logs', 'display_name' => 'View System Logs', 'category' => 'admin'],
                ['id' => 26, 'name' => 'admin.manage_settings', 'display_name' => 'Manage System Settings', 'category' => 'admin'],

                // Onboarding Management
                ['id' => 27, 'name' => 'onboarding.view', 'display_name' => 'View Onboarding Status', 'category' => 'onboarding'],
                ['id' => 28, 'name' => 'onboarding.reset', 'display_name' => 'Reset User Onboarding', 'category' => 'onboarding'],

                // ICT Operations
                ['id' => 29, 'name' => 'ict.view_tasks', 'display_name' => 'View ICT Tasks', 'category' => 'ict'],
                ['id' => 30, 'name' => 'ict.assign_tasks', 'display_name' => 'Assign ICT Tasks', 'category' => 'ict'],
                ['id' => 31, 'name' => 'ict.complete_tasks', 'display_name' => 'Complete ICT Tasks', 'category' => 'ict'],
                ['id' => 32, 'name' => 'ict.grant_access', 'display_name' => 'Grant System Access', 'category' => 'ict'],
            ];

            return response()->json([
                'success' => true,
                'data' => $permissions,
                'message' => 'Permissions retrieved successfully.'
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

    /**
     * Convert permission IDs to permission names for storage.
     */
    private function convertPermissionIds(array $permissionIds): array
    {
        if (empty($permissionIds)) {
            return [];
        }

        // Get all permissions and create a mapping
        $allPermissions = [
            1 => 'users.view', 2 => 'users.create', 3 => 'users.edit', 4 => 'users.delete', 5 => 'users.assign_roles',
            6 => 'roles.view', 7 => 'roles.create', 8 => 'roles.edit', 9 => 'roles.delete',
            10 => 'departments.view', 11 => 'departments.create', 12 => 'departments.edit', 13 => 'departments.delete', 14 => 'departments.assign_hod',
            15 => 'requests.view_all', 16 => 'requests.approve', 17 => 'requests.reject', 18 => 'requests.cancel',
            19 => 'devices.view', 20 => 'devices.create', 21 => 'devices.edit', 22 => 'devices.delete', 23 => 'devices.approve_booking',
            24 => 'admin.full_access', 25 => 'admin.view_logs', 26 => 'admin.manage_settings',
            27 => 'onboarding.view', 28 => 'onboarding.reset',
            29 => 'ict.view_tasks', 30 => 'ict.assign_tasks', 31 => 'ict.complete_tasks', 32 => 'ict.grant_access',
        ];

        $permissions = [];
        foreach ($permissionIds as $id) {
            if (isset($allPermissions[$id])) {
                $permissions[] = $allPermissions[$id];
            }
        }

        return $permissions;
    }
}
