<?php

namespace Tests\Feature;

use App\Models\BookingService;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ICTApprovalStatusTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that ICT approval updates the main booking status.
     *
     * @return void
     */
    public function test_ict_approval_updates_main_status()
    {
        // Skip if database is not configured
        if (!app('db')->connection()->getPdo()) {
            $this->markTestSkipped('Database not configured for testing');
        }

        // Create test user with ICT officer role
        $ictRole = Role::firstOrCreate(['name' => 'ict_officer']);
        $ictOfficer = User::factory()->create();
        $ictOfficer->role()->associate($ictRole);
        $ictOfficer->save();

        // Create regular user
        $regularUser = User::factory()->create();

        // Create booking service request
        $booking = BookingService::create([
            'user_id' => $regularUser->id,
            'borrower_name' => $regularUser->name,
            'phone_number' => '1234567890',
            'department' => 1,
            'device_type' => 'laptop',
            'booking_date' => now()->addDay(),
            'return_date' => now()->addWeek(),
            'return_time' => '17:00',
            'reason' => 'Testing purposes',
            'signature_path' => 'test/signature.png',
            'status' => 'pending',
            'ict_approve' => 'pending'
        ]);

        // Verify initial status
        $this->assertEquals('pending', $booking->status);
        $this->assertEquals('pending', $booking->ict_approve);

        // ICT officer approves the request
        $response = $this->actingAs($ictOfficer)
            ->postJson("/api/booking-service/bookings/{$booking->id}/ict-approve", [
                'ict_notes' => 'Approved for testing'
            ]);

        // Verify response
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Booking approved successfully by ICT Officer'
        ]);

        // Verify database update
        $booking->refresh();
        $this->assertEquals('approved', $booking->status); // Main status updated
        $this->assertEquals('approved', $booking->ict_approve); // ICT status updated
        $this->assertEquals($ictOfficer->id, $booking->ict_approved_by);
        $this->assertEquals($ictOfficer->id, $booking->approved_by);
        $this->assertNotNull($booking->ict_approved_at);
        $this->assertNotNull($booking->approved_at);
    }

    /**
     * Test that ICT rejection updates the main booking status.
     *
     * @return void
     */
    public function test_ict_rejection_updates_main_status()
    {
        // Skip if database is not configured
        if (!app('db')->connection()->getPdo()) {
            $this->markTestSkipped('Database not configured for testing');
        }

        // Create test user with ICT officer role
        $ictRole = Role::firstOrCreate(['name' => 'ict_officer']);
        $ictOfficer = User::factory()->create();
        $ictOfficer->role()->associate($ictRole);
        $ictOfficer->save();

        // Create regular user
        $regularUser = User::factory()->create();

        // Create booking service request
        $booking = BookingService::create([
            'user_id' => $regularUser->id,
            'borrower_name' => $regularUser->name,
            'phone_number' => '1234567890',
            'department' => 1,
            'device_type' => 'laptop',
            'booking_date' => now()->addDay(),
            'return_date' => now()->addWeek(),
            'return_time' => '17:00',
            'reason' => 'Testing purposes',
            'signature_path' => 'test/signature.png',
            'status' => 'pending',
            'ict_approve' => 'pending'
        ]);

        // ICT officer rejects the request
        $response = $this->actingAs($ictOfficer)
            ->postJson("/api/booking-service/bookings/{$booking->id}/ict-reject", [
                'ict_notes' => 'Device not available for testing'
            ]);

        // Verify response
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Booking rejected successfully by ICT Officer'
        ]);

        // Verify database update
        $booking->refresh();
        $this->assertEquals('rejected', $booking->status); // Main status updated
        $this->assertEquals('rejected', $booking->ict_approve); // ICT status updated
        $this->assertEquals($ictOfficer->id, $booking->ict_approved_by);
        $this->assertEquals($ictOfficer->id, $booking->approved_by);
        $this->assertNotNull($booking->ict_approved_at);
        $this->assertNotNull($booking->approved_at);
    }

    /**
     * Test that request status endpoint shows correct status after ICT approval.
     *
     * @return void
     */
    public function test_request_status_shows_ict_approval_status()
    {
        // Skip if database is not configured
        if (!app('db')->connection()->getPdo()) {
            $this->markTestSkipped('Database not configured for testing');
        }

        // Create regular user
        $regularUser = User::factory()->create();

        // Create booking service request with ICT approval
        $booking = BookingService::create([
            'user_id' => $regularUser->id,
            'borrower_name' => $regularUser->name,
            'phone_number' => '1234567890',
            'department' => 1,
            'device_type' => 'laptop',
            'booking_date' => now()->addDay(),
            'return_date' => now()->addWeek(),
            'return_time' => '17:00',
            'reason' => 'Testing purposes',
            'signature_path' => 'test/signature.png',
            'status' => 'approved',
            'ict_approve' => 'approved',
            'ict_approved_by' => 1,
            'ict_approved_at' => now(),
            'approved_by' => 1,
            'approved_at' => now()
        ]);

        // User checks their request status
        $response = $this->actingAs($regularUser)
            ->getJson('/api/request-status/details', [
                'id' => $booking->id,
                'type' => 'booking_service'
            ]);

        // Verify the status shows as approved
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'currentStatus' => 'approved',
                'ictStatus' => 'approved'
            ]
        ]);
    }
}
