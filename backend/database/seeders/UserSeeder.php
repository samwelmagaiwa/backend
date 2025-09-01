<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;

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
            ['name' => 'ict_director'],
            ['name' => 'staff'],
            ['name' => 'ict_officer'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }

        // Get departments for assignment
        $ictDepartment = Department::where('code', 'ICT')->first();
        $hrDepartment = Department::where('code', 'HR')->first();
        $adminDepartment = Department::where('code', 'ADMIN')->first();
        $nursingDepartment = Department::where('code', 'NURS')->first();
        $labDepartment = Department::where('code', 'LAB')->first();
        $pharmacyDepartment = Department::where('code', 'PHARM')->first();

        // Note: In production, users should be created through proper registration
        // or admin interface rather than seeding demo accounts.
        // This seeder only creates the necessary roles for the system.
        
        // Uncomment the following code only for development/testing purposes:

        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@gmail.com',
                'phone' => '+255700000000',
                'pf_number' => 'PF2367',
                'department_id' => $adminDepartment?->id,
                'password' => Hash::make('12345678'),
                'role_name' => 'admin'
            ],
            [
                'name' => 'ICT Officer',
                'email' => 'ict@gmail.com',
                'phone' => '+255700000001',
                'pf_number' => 'PF1289',
                'department_id' => $ictDepartment?->id,
                'password' => Hash::make('12345678'),
                'role_name' => 'ict_officer'
            ],
            [
                'name' => 'Staff Normal',
                'email' => 'staff@gmail.com',
                'phone' => '+255700000002',
                'pf_number' => 'PF3746',
                'department_id' => $nursingDepartment?->id,
                'password' => Hash::make('12345678'),
                'role_name' => 'staff'
            ],
            [
                'name' => 'Divisional Director',
                'email' => 'divisional_director@gmail.com',
                'phone' => '+255700000003',
                'pf_number' => 'PF6372',
                'department_id' => $adminDepartment?->id,
                'password' => Hash::make('12345678'),
                'role_name' => 'divisional_director'
            ],
            [
                'name' => 'ICT Director',
                'email' => 'ict_director@gmail.com',
                'phone' => '+255700000004',
                'pf_number' => 'PF8901',
                'department_id' => $ictDepartment?->id,
                'password' => Hash::make('12345678'),
                'role_name' => 'ict_director'
            ],
            [
                'name' => 'Head of Department - HR',
                'email' => 'hod_hr@gmail.com',
                'phone' => '+255700000005',
                'pf_number' => 'PF5432',
                'department_id' => $hrDepartment?->id,
                'password' => Hash::make('12345678'),
                'role_name' => 'head_of_department'
            ],
            [
                'name' => 'Head of Department - Nursing',
                'email' => 'hod_nursing@gmail.com',
                'phone' => '+255700000006',
                'pf_number' => 'PF7890',
                'department_id' => $nursingDepartment?->id,
                'password' => Hash::make('12345678'),
                'role_name' => 'head_of_department'
            ],
            [
                'name' => 'Lab Technician',
                'email' => 'lab_tech@gmail.com',
                'phone' => '+255700000007',
                'pf_number' => 'PF4567',
                'department_id' => $labDepartment?->id,
                'password' => Hash::make('12345678'),
                'role_name' => 'staff'
            ],
            [
                'name' => 'Pharmacist',
                'email' => 'pharmacist@gmail.com',
                'phone' => '+255700000008',
                'pf_number' => 'PF9876',
                'department_id' => $pharmacyDepartment?->id,
                'password' => Hash::make('12345678'),
                'role_name' => 'staff'
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

        // After creating users, update departments with HOD assignments
        $this->assignHeadsOfDepartments();
    }

    /**
     * Assign heads of departments after users are created
     */
    private function assignHeadsOfDepartments(): void
    {
        // Assign ICT Director as HOD of ICT Department
        $ictDirector = User::where('email', 'ict_director@gmail.com')->first();
        $ictDepartment = Department::where('code', 'ICT')->first();
        if ($ictDirector && $ictDepartment) {
            $ictDepartment->update(['hod_user_id' => $ictDirector->id]);
        }

        // Assign HR HOD
        $hrHod = User::where('email', 'hod_hr@gmail.com')->first();
        $hrDepartment = Department::where('code', 'HR')->first();
        if ($hrHod && $hrDepartment) {
            $hrDepartment->update(['hod_user_id' => $hrHod->id]);
        }

        // Assign Nursing HOD
        $nursingHod = User::where('email', 'hod_nursing@gmail.com')->first();
        $nursingDepartment = Department::where('code', 'NURS')->first();
        if ($nursingHod && $nursingDepartment) {
            $nursingDepartment->update(['hod_user_id' => $nursingHod->id]);
        }
    }
}