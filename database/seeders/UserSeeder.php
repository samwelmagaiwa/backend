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

        // Create test users
        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@mnh.go.tz',
                'phone' => '255712000001',
                'pf_number' => 'MLG.0001',
                'password' => Hash::make('password123'),
                'role_name' => 'admin'
            ],
            [
                'name' => 'Divisional Director',
                'email' => 'divisional@mnh.go.tz',
                'phone' => '255712000002',
                'pf_number' => 'MLG.0002',
                'password' => Hash::make('password123'),
                'role_name' => 'divisional_director'
            ],
            [
                'name' => 'Head of Department',
                'email' => 'hod@mnh.go.tz',
                'phone' => '255712000003',
                'pf_number' => 'MLG.0003',
                'password' => Hash::make('password123'),
                'role_name' => 'head_of_department'
            ],
            [
                'name' => 'Head of IT Department',
                'email' => 'hod.it@mnh.go.tz',
                'phone' => '255712000004',
                'pf_number' => 'MLG.0004',
                'password' => Hash::make('hodit2024'),
                'role_name' => 'hod_it'
            ],
            [
                'name' => 'ICT Director',
                'email' => 'ict.director@mnh.go.tz',
                'phone' => '255712000005',
                'pf_number' => 'MLG.0005',
                'password' => Hash::make('password123'),
                'role_name' => 'ict_director'
            ],
            [
                'name' => 'Hospital Staff',
                'email' => 'staff@mnh.go.tz',
                'phone' => '255712000006',
                'pf_number' => 'MLG.0006',
                'password' => Hash::make('password123'),
                'role_name' => 'staff'
            ],
            [
                'name' => 'ICT Officer',
                'email' => 'ict.officer@mnh.go.tz',
                'phone' => '255712000007',
                'pf_number' => 'MLG.0007',
                'password' => Hash::make('password123'),
                'role_name' => 'ict_officer'
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
    }
}