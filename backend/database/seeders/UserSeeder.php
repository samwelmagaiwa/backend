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
                'department_id' => null,
                'password' => Hash::make('12345678'),
                'role_name' => 'ict_officer'
            ],
            [
                'name' => 'Staff Normal',
                'email' => 'staff@gmail.com',
                'phone' => '+255700000002',
                'pf_number' => 'PF3746',
                'department_id' => null,
                'password' => Hash::make('12345678'),
                'role_name' => 'staff'
            ],
            [
                'name' => 'Divisional Director',
                'email' => 'divisional_director@gmail.com',
                'phone' => '+255700000003',
                'pf_number' => 'PF6372',
                'department_id' => null,
                'password' => Hash::make('12345678'),
                'role_name' => 'divisional_director'
            ],
            [
                'name' => 'ICT Director',
                'email' => 'ict_director@gmail.com',
                'phone' => '+255700000004',
                'pf_number' => 'PF8901',
                'department_id' => null,
                'password' => Hash::make('12345678'),
                'role_name' => 'ict_director'
            ],
            [
                'name' => 'Head of Department - HR',
                'email' => 'hod_hr@gmail.com',
                'phone' => '+255700000005',
                'pf_number' => 'PF5432',
                'department_id' => null,
                'password' => Hash::make('12345678'),
                'role_name' => 'head_of_department'
            ],
            [
                'name' => 'Head of Department - Nursing',
                'email' => 'hod_nursing@gmail.com',
                'phone' => '+255700000006',
                'pf_number' => 'PF7890',
                'department_id' => null,
                'password' => Hash::make('12345678'),
                'role_name' => 'head_of_department'
            ],
            [
                'name' => 'Lab Technician',
                'email' => 'lab_tech@gmail.com',
                'phone' => '+255700000007',
                'pf_number' => 'PF4567',
                'department_id' => null,
                'password' => Hash::make('12345678'),
                'role_name' => 'staff'
            ],
            [
                'name' => 'Pharmacist',
                'email' => 'pharmacist@gmail.com',
                'phone' => '+255700000008',
                'pf_number' => 'PF9876',
                'department_id' => null,
                'password' => Hash::make('12345678'),
                'role_name' => 'staff'
            ],
             [
        'name' => 'John Mwanga',
        'email' => 'john.mwanga@gmail.com',
        'phone' => '+255700000009',
        'pf_number' => 'PF1122',
        'department_id' => null,
        'password' => Hash::make('12345678'),
        'role_name' => 'staff'
    ],
    [
        'name' => 'Asha Juma',
        'email' => 'asha.juma@gmail.com',
        'phone' => '+255700000010',
        'pf_number' => 'PF3344',
        'department_id' => null,
        'password' => Hash::make('12345678'),
        'role_name' => 'staff'
    ],
    [
        'name' => 'David Selemani',
        'email' => 'david.selemani@gmail.com',
        'phone' => '+255700000011',
        'pf_number' => 'PF5566',
        'department_id' => null,
        'password' => Hash::make('12345678'),
        'role_name' => 'staff'
    ],
    [
        'name' => 'Fatuma Bakari',
        'email' => 'fatuma.bakari@gmail.com',
        'phone' => '+255700000012',
        'pf_number' => 'PF7788',
        'department_id' => null,
        'password' => Hash::make('12345678'),
        'role_name' => 'staff'
    ],
    [
        'name' => 'Samuel Nyerere',
        'email' => 'samuel.nyerere@gmail.com',
        'phone' => '+255700000013',
        'pf_number' => 'PF9900',
        'department_id' => null,
        'password' => Hash::make('12345678'),
        'role_name' => 'staff'
    ],
    [
        'name' => 'Rashid Ali',
        'email' => 'rashid.ali@gmail.com',
        'phone' => '+255700000014',
        'pf_number' => 'PF2233',
        'department_id' => null,
        'password' => Hash::make('12345678'),
        'role_name' => 'staff'
    ],
    [
        'name' => 'Neema Msuya',
        'email' => 'neema.msuya@gmail.com',
        'phone' => '+255700000015',
        'pf_number' => 'PF4455',
        'department_id' => null,
        'password' => Hash::make('12345678'),
        'role_name' => 'staff'
    ],
    [
        'name' => 'Mohamed Suleiman',
        'email' => 'mohamed.suleiman@gmail.com',
        'phone' => '+255700000016',
        'pf_number' => 'PF6677',
        'department_id' => null,
        'password' => Hash::make('12345678'),
        'role_name' => 'staff'
    ],
    [
        'name' => 'Amina Hassan',
        'email' => 'amina.hassan@gmail.com',
        'phone' => '+255700000017',
        'pf_number' => 'PF8899',
        'department_id' => null,
        'password' => Hash::make('12345678'),
        'role_name' => 'staff'
    ],
    [
        'name' => 'Peter Komba',
        'email' => 'peter.komba@gmail.com',
        'phone' => '+255700000018',
        'pf_number' => 'PF1010',
        'department_id' => null,
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
        //$this->assignHeadsOfDepartments();
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