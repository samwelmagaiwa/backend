<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\User;
use App\Models\Role;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Note: HOD assignments will be done after users are created
        // This seeder only creates the department structure

        $departments = [
            [
                'name' => 'Information and Communication Technology',
                'code' => 'ICT',
                'description' => 'Manages hospital IT infrastructure and systems',
                'hod_user_id' => null, // Will be assigned after users are created
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Manages staff recruitment, training, and welfare',
                'hod_user_id' => null, // Will be assigned after users are created
                'is_active' => true,
            ],
            [
                'name' => 'Medical Records',
                'code' => 'MR',
                'description' => 'Manages patient medical records and documentation',
                'hod_user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Nursing Department',
                'code' => 'NURS',
                'description' => 'Provides nursing care and patient support',
                'hod_user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Laboratory Services',
                'code' => 'LAB',
                'description' => 'Provides diagnostic laboratory services',
                'hod_user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Pharmacy Department',
                'code' => 'PHARM',
                'description' => 'Manages medication dispensing and pharmaceutical care',
                'hod_user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Radiology Department',
                'code' => 'RAD',
                'description' => 'Provides medical imaging and diagnostic services',
                'hod_user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Department',
                'code' => 'ED',
                'description' => 'Provides emergency medical care',
                'hod_user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Surgery Department',
                'code' => 'SURG',
                'description' => 'Provides surgical services and operating room management',
                'hod_user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Outpatient Department',
                'code' => 'OPD',
                'description' => 'Manages outpatient consultations and services',
                'hod_user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Finance Department',
                'code' => 'FIN',
                'description' => 'Manages hospital finances and billing',
                'hod_user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Administration',
                'code' => 'ADMIN',
                'description' => 'Hospital administration and management',
                'hod_user_id' => null,
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['code' => $department['code']],
                $department
            );
        }
    }
}