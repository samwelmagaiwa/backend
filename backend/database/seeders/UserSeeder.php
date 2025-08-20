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
                'email' => 'admin@gmail.com',
                'phone' => '255712000001',
                'pf_number' => null, // Admin doesn't need PF number
                'password' => Hash::make('12345678'),
                'role_name' => 'admin'
            ],
            [
                'name' => 'Divisional Director',
                'email' => 'divisional@gmail.com',
                'phone' => '255712000002',
                'pf_number' => 'DD001', // Unique PF number
                'password' => Hash::make('12345678'),
                'role_name' => 'divisional_director'
            ],
            [
                'name' => 'Head of Department',
                'email' => 'hod@gmail.com',
                'phone' => '255712000003',
                'pf_number' => 'HOD001', // Unique PF number
                'password' => Hash::make('12345678'),
                'role_name' => 'head_of_department'
            ],
            [
                'name' => 'Head of IT Department',
                'email' => 'hod.it@gmail.com',
                'phone' => '255712000004',
                'pf_number' => 'HODIT001', // Unique PF number
                'password' => Hash::make('12345678'),
                'role_name' => 'hod_it'
            ],
            [
                'name' => 'ICT Director',
                'email' => 'ict.director@gmail.com',
                'phone' => '255712000005',
                'pf_number' => 'ICTDIR001', // Unique PF number
                'password' => Hash::make('12345678'),
                'role_name' => 'ict_director'
            ],
            [
                'name' => 'Hospital Staff',
                'email' => 'staff@gmail.com',
                'phone' => '255712000006',
                'pf_number' => 'STAFF001', // Unique PF number
                'password' => Hash::make('12345678'),
                'role_name' => 'staff'
            ],
            [
                'name' => 'ICT Officer',
                'email' => 'ict.officer@gmail.com',
                'phone' => '255712000007',
                'pf_number' => 'ICTOFC001', // Unique PF number
                'password' => Hash::make('12345678'),
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