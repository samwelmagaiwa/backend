<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    /**
     * Test the new role system
     */
    public function testRoleSystem(Request $request): JsonResponse
    {
        try {
            $data = [
                'system_status' => 'operational',
                'role_system' => 'many-to-many',
                'roles_available' => Role::orderBy('sort_order')->get(['id', 'name', 'description']),
                'total_users' => User::count(),
                'users_with_roles' => User::has('roles')->count(),
                'users_without_roles' => User::doesntHave('roles')->count(),
                'current_user' => null
            ];

            if ($request->user()) {
                $user = $request->user();
                $user->load('roles');
                
                $data['current_user'] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')->toArray(),
                    'display_roles' => $user->getDisplayRoleNames(),
                    'permissions' => $user->getAllPermissions(),
                    'is_admin' => $user->isAdmin(),
                    'has_admin_privileges' => $user->hasAdminPrivileges(),
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Role system test completed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role system test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all available roles for frontend
     */
    public function getRoles(): JsonResponse
    {
        try {
            $roles = Role::orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'description', 'is_system_role']);

            $transformedRoles = $roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => ucwords(str_replace('_', ' ', $role->name)),
                    'description' => $role->description,
                    'is_system_role' => $role->is_system_role
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedRoles,
                'message' => 'Roles retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}