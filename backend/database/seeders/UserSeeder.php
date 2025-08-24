<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates the necessary roles for the hospital management system.
     * For production use, create users through the admin interface or proper
     * registration process instead of seeding demo accounts.
     */
    public function run(): void
    {
        // Create roles first
        $roles = [
            ['name' => 'admin'],
            ['name' => 'divisional_director'],
            ['name' => 'head_of_department'],
            ['name' => 'hod_it'],
            ['name' => 'ict_director'],
            ['name' => 'staff'],
            ['name' => 'ict_officer'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }

        // Note: In production, users should be created through proper registration
        // or admin interface rather than seeding demo accounts.
        // This seeder only creates the necessary roles for the system.
        
        // Uncomment the following code only for development/testing purposes:
        /*
        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@hospital.go.tz',
                'phone' => '+255700000000',
                'pf_number' => null,
                'password' => Hash::make('secure_password_here'),
                'role_name' => 'admin'
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role_name'];
            unset($userData['role_name']);
            
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $userData['role_id'] = $role->id;
                User::firstOrCreate(['email' => $userData['email']], $userData);
            }
        }
        */
    }
}