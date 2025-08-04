<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        // Role::firstOrCreate(['name' => 'admin']);
        // Role::firstOrCreate(['name' => 'doctor']);
        // Role::firstOrCreate(['name' => 'lab']);
        // Role::firstOrCreate(['name' => 'radiology']);
        // Role::firstOrCreate(['name' => 'nurse']);
        // Role::firstOrCreate(['name' => 'ict']);

        $roles = ['admin', 'divisional_director','head of department', 'ict director', 'head of it', 'staff', 'ict officer',];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
     }
}