<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoleManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder sets up the role management system with proper permissions.
     * User role assignments should be done through the admin interface in production.
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Update existing roles with new fields
            $roleUpdates = [
                'admin' => [
                    'description' => 'System administrator with full access to all features',
                    'permissions' => [
                        'create_users', 'edit_users', 'delete_users', 'view_users',
                        'create_roles', 'edit_roles', 'delete_roles', 'assign_roles',
                        'create_departments', 'edit_departments', 'delete_departments', 'assign_hod',
                        'view_all_requests', 'approve_requests', 'reject_requests', 'export_requests',
                        'system_settings', 'audit_logs', 'backup_restore', 'maintenance_mode'
                    ],
                    'is_system_role' => true,
                    'is_deletable' => false,
                    'sort_order' => 1
                ],
                'divisional_director' => [
                    'description' => 'Divisional director with approval authority',
                    'permissions' => [
                        'view_users', 'view_all_requests', 'approve_requests', 'reject_requests'
                    ],
                    'is_system_role' => true,
                    'is_deletable' => false,
                    'sort_order' => 2
                ],
                'head_of_department' => [
                    'description' => 'Head of department with departmental oversight',
                    'permissions' => [
                        'view_users', 'view_all_requests', 'approve_requests', 'reject_requests'
                    ],
                    'is_system_role' => true,
                    'is_deletable' => false,
                    'sort_order' => 3
                ],
                'hod_it' => [
                    'description' => 'Head of IT department',
                    'permissions' => [
                        'view_users', 'view_all_requests', 'approve_requests', 'reject_requests',
                        'system_settings'
                    ],
                    'is_system_role' => true,
                    'is_deletable' => false,
                    'sort_order' => 4
                ],
                'ict_director' => [
                    'description' => 'ICT director with technical oversight',
                    'permissions' => [
                        'view_users', 'view_all_requests', 'approve_requests', 'reject_requests',
                        'system_settings', 'audit_logs'
                    ],
                    'is_system_role' => true,
                    'is_deletable' => false,
                    'sort_order' => 5
                ],
                'ict_officer' => [
                    'description' => 'ICT officer with technical support responsibilities',
                    'permissions' => [
                        'view_users', 'view_all_requests', 'approve_requests', 'reject_requests'
                    ],
                    'is_system_role' => true,
                    'is_deletable' => false,
                    'sort_order' => 6
                ],
                'staff' => [
                    'description' => 'Regular hospital staff member',
                    'permissions' => [
                        'view_users'
                    ],
                    'is_system_role' => true,
                    'is_deletable' => false,
                    'sort_order' => 7
                ]
            ];

            foreach ($roleUpdates as $roleName => $updates) {
                Role::where('name', $roleName)->update($updates);
            }

            // Create super admin role if it doesn't exist
            Role::firstOrCreate(
                ['name' => 'super_admin'],
                [
                    'description' => 'Super administrator with unrestricted access',
                    'permissions' => [
                        'create_users', 'edit_users', 'delete_users', 'view_users',
                        'create_roles', 'edit_roles', 'delete_roles', 'assign_roles',
                        'create_departments', 'edit_departments', 'delete_departments', 'assign_hod',
                        'view_all_requests', 'approve_requests', 'reject_requests', 'export_requests',
                        'system_settings', 'audit_logs', 'backup_restore', 'maintenance_mode'
                    ],
                    'is_system_role' => true,
                    'is_deletable' => false,
                    'sort_order' => 0
                ]
            );

            // Note: Role assignments should be done through the admin interface
            // or proper user management system in production.
            // Uncomment the following code only for development/testing:
            /*
            $userRoleAssignments = [
                'admin@hospital.go.tz' => ['admin'],
            ];

            foreach ($userRoleAssignments as $email => $roleNames) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $roles = Role::whereIn('name', $roleNames)->get();
                    
                    $roleData = [];
                    foreach ($roles as $role) {
                        $roleData[$role->id] = [
                            'assigned_at' => now(),
                            'assigned_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    
                    $user->roles()->sync($roleData);
                }
            }
            */

            DB::commit();
            
            $this->command->info('Role management system seeded successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding role management: ' . $e->getMessage());
            throw $e;
        }
    }
}