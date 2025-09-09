<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookingService;
use App\Models\User;
use App\Models\Department;
use Carbon\Carbon;

class BookingServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if we already have booking data
        if (BookingService::count() > 0) {
            $this->command->info('Booking service data already exists. Skipping seeder.');
            return;
        }

        // Get some users to create bookings for
        $users = User::limit(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        // Get departments
        $departments = Department::pluck('name')->toArray();
        if (empty($departments)) {
            $departments = ['ICT Department', 'Administration', 'Finance'];
        }

        $deviceTypes = ['projector', 'monitor', 'cpu', 'keyboard', 'laptop'];
        $statuses = ['pending', 'approved', 'rejected'];

        foreach ($users as $user) {
            // Create 2-3 bookings per user
            for ($i = 0; $i < rand(2, 3); $i++) {
                $bookingDate = Carbon::now()->subDays(rand(1, 30));
                $returnDate = $bookingDate->copy()->addDays(rand(1, 7));

                BookingService::create([
                    'user_id' => $user->id,
                    'booking_date' => $bookingDate->format('Y-m-d'),
                    'borrower_name' => $user->name,
                    'device_type' => $deviceTypes[array_rand($deviceTypes)],
                    'custom_device' => null,
                    'department' => $departments[array_rand($departments)],
                    'phone_number' => $user->phone ?? '0700000000',
                    'return_date' => $returnDate->format('Y-m-d'),
                    'return_time' => '17:00:00',
                    'reason' => 'Test booking for ' . $deviceTypes[array_rand($deviceTypes)] . ' usage',
                    'status' => $statuses[array_rand($statuses)],
                    'created_at' => $bookingDate,
                    'updated_at' => $bookingDate,
                ]);
            }
        }

        $this->command->info('Created ' . BookingService::count() . ' test booking records.');
    }
}