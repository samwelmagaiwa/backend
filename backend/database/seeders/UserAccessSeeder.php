<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// Uncomment these imports when enabling demo data:
// use App\Models\UserAccess;
// use App\Models\User;
// use App\Models\Department;

class UserAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder is intended for development/testing purposes only.
     * In production, user access requests should be created through
     * the application interface by actual users.
     */
    public function run(): void
    {
        // Note: This seeder creates sample user access requests for development/testing.
        // In production, remove or comment out this seeder to avoid creating demo data.
        
        // Uncomment the following code only for development/testing purposes:
        /*
        $staffUser = User::whereHas('role', function($query) {
            $query->where('name', 'staff');
        })->first();
        
        $hrDepartment = Department::where('code', 'HR')->first();

        if ($staffUser && $hrDepartment) {
            $sampleRequests = [
                [
                    'user_id' => $staffUser->id,
                    'pf_number' => 'TEST001',
                    'staff_name' => 'Test User',
                    'phone_number' => '+255700000001',
                    'department_id' => $hrDepartment->id,
                    'request_type' => ['jeeva_access'],
                    'purpose' => ['Testing purposes'],
                    'status' => 'pending',
                    'signature_path' => 'signatures/test/signature1.png',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($sampleRequests as $requestData) {
                UserAccess::firstOrCreate(
                    [
                        'pf_number' => $requestData['pf_number'],
                        'staff_name' => $requestData['staff_name']
                    ],
                    $requestData
                );
            }
        }
        */
    }
}