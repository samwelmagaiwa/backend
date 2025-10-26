<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        // Check if the roles table has the required columns
        if (!Schema::hasTable('roles')) {
            $this->command->warn('Roles table does not exist. Skipping role seeding.');
            return;
        }

        // Check for required columns
        $requiredColumns = ['description', 'permissions', 'is_system_role', 'is_deletable', 'sort_order'];
        $missingColumns = [];
        
        foreach ($requiredColumns as $column) {
            if (!Schema::hasColumn('roles', $column)) {
                $missingColumns[] = $column;
            }
        }
        
        if (!empty($missingColumns)) {
            $this->command->warn('Missing columns in roles table: ' . implode(', ', $missingColumns) . '. Skipping role seeding.');
            return;
        }

        DB::beginTransaction();
        
        try {
            // Create or update roles with new fields
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
                    'sort_order' => 0
                ],
                'divisional_director' => [
                    'description' => 'Divisional director with approval authority',
                    'permissions' => [
                        'view_users', 'view_all_requests', 'approve_requests', 'reject_requests'
                    ],
                    'is_system_role' => true,
                    'is_deletable' => false,
                    'sort_order' => 1
                ],
                'head_of_department' => [
                    'description' => 'Head of department with departmental oversight',
                    'permissions' => [
                        'view_users', 'view_all_requests', 'approve_requests', 'reject_requests'
                    ],
                    'is_system_role' => true,
                    'is_deletable' => false,
                    'sort_order' => 2
                ],
                'ict_director' => [
                    'description' => 'ICT director with technical oversight',
                    'permissions' => [
                        'view_users', 'view_all_requests', 'approve_requests', 'reject_requests',
                        'system_settings', 'audit_logs'
                    ],
                    'is_system_role' => true,
                    'is_deletable' => false,
                    'sort_order' => 3
                ],
                'dict' => [
                    'description' => 'Director of ICT with full technical authority',
                    'permissions' => [
                        'view_users', 'view_all_requests', 'approve_requests', 'reject_requests',
                        'system_settings', 'audit_logs'
                    ],
                    'is_system_role' => true,
                    'is_deletable' => false,
                    'sort_order' => 4
                ],
                'head_of_it' => [
                    'description' => 'Head of IT with technical authority and assignment responsibilities',
                    'permissions' => [
                        'view_users', 'view_all_requests', 'approve_requests', 'reject_requests',
                        'assign_ict_officer', 'manage_implementations'
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
                Role::updateOrCreate(
                    ['name' => $roleName],
                    $updates
                );
            }

            // Ensure all users have roles in the new system
            $this->migrateUserRoles();

            DB::commit();
            
            $this->command->info('Role management system seeded successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding role management: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Migrate users from old single role system to new many-to-many system
     */
    private function migrateUserRoles(): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            // Check if user already has roles in the new system
            $existingRoles = $user->roles()->count();
            
            if ($existingRoles === 0) {
                // Check if user has role_id column and value
                if (Schema::hasColumn('users', 'role_id') && $user->role_id) {
                    $role = Role::find($user->role_id);
                    if ($role) {
                        $user->roles()->attach($user->role_id, [
                            'assigned_at' => $user->created_at ?? now(),
                            'assigned_by' => 1, // System migration
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        $this->command->info("Migrated user {$user->email} from role {$role->name}");
                    }
                } else {
                    // Assign default 'staff' role if no role exists
                    $staffRole = Role::where('name', 'staff')->first();
                    if ($staffRole) {
                        $user->roles()->attach($staffRole->id, [
                            'assigned_at' => $user->created_at ?? now(),
                            'assigned_by' => 1, // System migration
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        $this->command->info("Assigned default staff role to user {$user->email}");
                    }
                }
            }
        }
    }
}