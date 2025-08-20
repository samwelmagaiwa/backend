<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Administration', 'code' => 'ADMIN', 'description' => 'Administrative Department'],
            ['name' => 'Cardiology', 'code' => 'CARD', 'description' => 'Cardiology Department'],
            ['name' => 'Dermatology', 'code' => 'DERM', 'description' => 'Dermatology Department'],
            ['name' => 'Emergency Medicine', 'code' => 'EMRG', 'description' => 'Emergency Medicine Department'],
            ['name' => 'Endocrinology', 'code' => 'ENDO', 'description' => 'Endocrinology Department'],
            ['name' => 'Finance', 'code' => 'FIN', 'description' => 'Finance Department'],
            ['name' => 'Gastroenterology', 'code' => 'GAST', 'description' => 'Gastroenterology Department'],
            ['name' => 'General Surgery', 'code' => 'GSUR', 'description' => 'General Surgery Department'],
            ['name' => 'Gynecology', 'code' => 'GYNE', 'description' => 'Gynecology Department'],
            ['name' => 'Hematology', 'code' => 'HEMA', 'description' => 'Hematology Department'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'Human Resources Department'],
            ['name' => 'ICT Department', 'code' => 'ICT', 'description' => 'Information and Communication Technology Department'],
            ['name' => 'Internal Medicine', 'code' => 'INTM', 'description' => 'Internal Medicine Department'],
            ['name' => 'Laboratory', 'code' => 'LAB', 'description' => 'Laboratory Department'],
            ['name' => 'Nephrology', 'code' => 'NEPH', 'description' => 'Nephrology Department'],
            ['name' => 'Neurology', 'code' => 'NEUR', 'description' => 'Neurology Department'],
            ['name' => 'Nursing', 'code' => 'NURS', 'description' => 'Nursing Department'],
            ['name' => 'Obstetrics', 'code' => 'OBST', 'description' => 'Obstetrics Department'],
            ['name' => 'Oncology', 'code' => 'ONCO', 'description' => 'Oncology Department'],
            ['name' => 'Ophthalmology', 'code' => 'OPHT', 'description' => 'Ophthalmology Department'],
            ['name' => 'Orthopedics', 'code' => 'ORTH', 'description' => 'Orthopedics Department'],
            ['name' => 'Pediatrics', 'code' => 'PEDI', 'description' => 'Pediatrics Department'],
            ['name' => 'Pharmacy', 'code' => 'PHAR', 'description' => 'Pharmacy Department'],
            ['name' => 'Psychiatry', 'code' => 'PSYC', 'description' => 'Psychiatry Department'],
            ['name' => 'Pulmonology', 'code' => 'PULM', 'description' => 'Pulmonology Department'],
            ['name' => 'Radiology', 'code' => 'RADI', 'description' => 'Radiology Department'],
            ['name' => 'Rheumatology', 'code' => 'RHEU', 'description' => 'Rheumatology Department'],
            ['name' => 'Urology', 'code' => 'UROL', 'description' => 'Urology Department'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['code' => $department['code']],
                [
                    'name' => $department['name'],
                    'description' => $department['description'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Departments seeded successfully!');
    }
}