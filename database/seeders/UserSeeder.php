<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin', 'email' => 'admin@gmail.com', 'role' => 'admin', 'pf_number' => 'MLG.1234'],
            ['name' => 'HoD', 'email' => 'hod@gmail.com', 'role' => 'head of department', 'pf_number' => 'MLG.3435'],
            ['name' => 'divisional_director', 'email' => 'division@gmail.com', 'role' => 'divisional_director', 'pf_number' => 'MLG.3432'],
            ['name' => 'ICT Director', 'email' => 'ictdirector@gmail.com', 'role' => 'ict director', 'pf_number' => 'MLG.3439'],
            ['name' => 'Head of IT', 'email' => 'headofit@gmail.com', 'role' => 'head of it', 'pf_number' => 'MLG.3438'],
            ['name' => 'Ict Officer', 'email' => 'itassigned@gmail.com', 'role' => 'ict officer', 'pf_number' => 'MLG.3437'],
        ];

        foreach ($users as $index => $u) {
            $role = Role::where('name', $u['role'])->first();

            $phone = '255743' . str_pad($index + 1000, 4, '0', STR_PAD_LEFT); // generates unique: 2557431000, 2557431001, etc.

            User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('12345678'),
                    'pf_number' => $u['pf_number'],
                    'phone' => $phone,
                    'role_id' => $role?->id,
                ]
            );
        }
    }
}
