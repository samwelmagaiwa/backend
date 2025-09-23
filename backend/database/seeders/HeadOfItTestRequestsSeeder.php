<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAccess;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HeadOfItTestRequestsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        Log::info('HeadOfItTestRequestsSeeder: Starting test requests seeding');
        
        // Find some departments
        $departments = Department::take(5)->get();
        if ($departments->count() < 3) {
            $this->command->error('❌ Need at least 3 departments in the database first');
            return;
        }

        // Find or create test staff users
        $testUsers = [
            [
                'name' => 'Dr. Patricia Williams',
                'email' => 'patricia.williams@mnh.go.tz',
                'pf_number' => 'MED001',
                'phone' => '+255712345690',
                'department_id' => $departments[0]->id,
            ],
            [
                'name' => 'Robert Davis',
                'email' => 'robert.davis@mnh.go.tz',
                'pf_number' => 'ADM001',
                'phone' => '+255712345691',
                'department_id' => $departments[1]->id,
            ],
            [
                'name' => 'Dr. Linda Garcia',
                'email' => 'linda.garcia@mnh.go.tz',
                'pf_number' => 'NUR001',
                'phone' => '+255712345692',
                'department_id' => $departments[2]->id,
            ],
        ];

        DB::beginTransaction();
        
        try {
            $users = [];
            foreach ($testUsers as $userData) {
                $users[] = User::updateOrCreate(
                    ['email' => $userData['email']],
                    array_merge($userData, [
                        'password' => bcrypt('password123'),
                        'is_active' => true
                    ])
                );
            }

            // Create test access requests at ict_director_approved status
            $testRequests = [
                [
                    'user_id' => $users[0]->id,
                    'request_id' => 'REQ-' . str_pad(1, 6, '0', STR_PAD_LEFT),
                    'pf_number' => 'MED001',
                    'staff_name' => 'Dr. Patricia Williams',
                    'phone_number' => '+255712345690',
                    'department_id' => $departments[0]->id,
                    'department_name' => $departments[0]->name,
                    'position' => 'Senior Physician',
                    'request_types' => 'jeeva_access,wellsoft',
                    'internet_purposes' => json_encode(['research', 'clinical_data']),
                    'wellsoft_modules_selected' => json_encode(['patient_management', 'billing']),
                    'jeeva_modules_selected' => json_encode(['appointments', 'records']),
                    'access_type' => 'permanent',
                    'status' => 'ict_director_approved',
                    'hod_status' => 'approved',
                    'divisional_status' => 'approved',
                    'ict_director_status' => 'approved',
                    'hod_approved_at' => now()->subDays(5),
                    'hod_approved_by' => 2,
                    'hod_name' => 'Dr. John Smith',
                    'divisional_approved_at' => now()->subDays(3),
                    'divisional_director_name' => 'Mary Johnson',
                    'ict_director_approved_at' => now()->subDays(1),
                    'ict_director_name' => 'David Wilson',
                    'created_at' => now()->subDays(7),
                    'updated_at' => now()->subDays(1)
                ],
                [
                    'user_id' => $users[1]->id,
                    'request_id' => 'REQ-' . str_pad(2, 6, '0', STR_PAD_LEFT),
                    'pf_number' => 'ADM001',
                    'staff_name' => 'Robert Davis',
                    'phone_number' => '+255712345691',
                    'department_id' => $departments[1]->id,
                    'department_name' => $departments[1]->name,
                    'position' => 'Administrator',
                    'request_types' => 'internet_access_request',
                    'internet_purposes' => json_encode(['email', 'administrative_work', 'research']),
                    'access_type' => 'permanent',
                    'status' => 'ict_director_approved',
                    'hod_status' => 'approved',
                    'divisional_status' => 'approved',
                    'ict_director_status' => 'approved',
                    'hod_approved_at' => now()->subDays(6),
                    'hod_approved_by' => 3,
                    'hod_name' => 'Sarah Brown',
                    'divisional_approved_at' => now()->subDays(4),
                    'divisional_director_name' => 'Michael Taylor',
                    'ict_director_approved_at' => now()->subHours(12),
                    'ict_director_name' => 'David Wilson',
                    'created_at' => now()->subDays(8),
                    'updated_at' => now()->subHours(12)
                ],
                [
                    'user_id' => $users[2]->id,
                    'request_id' => 'REQ-' . str_pad(3, 6, '0', STR_PAD_LEFT),
                    'pf_number' => 'NUR001',
                    'staff_name' => 'Dr. Linda Garcia',
                    'phone_number' => '+255712345692',
                    'department_id' => $departments[2]->id,
                    'department_name' => $departments[2]->name,
                    'position' => 'Head Nurse',
                    'request_types' => 'wellsoft,internet_access_request',
                    'wellsoft_modules_selected' => json_encode(['patient_records', 'medication_tracking']),
                    'internet_purposes' => json_encode(['clinical_research', 'email']),
                    'access_type' => 'temporary',
                    'temporary_until' => now()->addMonths(6),
                    'status' => 'ict_director_approved',
                    'hod_status' => 'approved',
                    'divisional_status' => 'approved',
                    'ict_director_status' => 'approved',
                    'hod_approved_at' => now()->subDays(4),
                    'hod_approved_by' => 4,
                    'hod_name' => 'Dr. Emma Wilson',
                    'divisional_approved_at' => now()->subDays(2),
                    'divisional_director_name' => 'James Anderson',
                    'ict_director_approved_at' => now()->subHours(6),
                    'ict_director_name' => 'David Wilson',
                    'created_at' => now()->subDays(6),
                    'updated_at' => now()->subHours(6)
                ]
            ];

            foreach ($testRequests as $requestData) {
                UserAccess::create($requestData);
                $this->command->info("✅ Created test request: {$requestData['request_id']} - {$requestData['staff_name']}");
            }

            DB::commit();
            
            $this->command->info('🎯 Successfully created 3 test access requests ready for Head of IT approval:');
            $this->command->info('   1. Dr. Patricia Williams - Jeeva + Wellsoft Access (Permanent)');
            $this->command->info('   2. Robert Davis - Internet Access (Permanent)');
            $this->command->info('   3. Dr. Linda Garcia - Wellsoft + Internet (Temporary - 6 months)');
            $this->command->info('');
            $this->command->info('📋 All requests are at status: ict_director_approved');
            $this->command->info('🎭 Ready for Head of IT review and approval');
            
            Log::info('HeadOfItTestRequestsSeeder: Successfully completed test requests seeding');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('HeadOfItTestRequestsSeeder: Error during seeding', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->command->error('❌ Error creating test requests: ' . $e->getMessage());
            throw $e;
        }
    }
}
