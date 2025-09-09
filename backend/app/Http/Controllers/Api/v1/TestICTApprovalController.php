<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\BookingService;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Test ICT Approval Controller
 * 
 * Provides test endpoints to validate the ICT approval system
 * This controller should be removed in production
 */
class TestICTApprovalController extends Controller
{
    /**
     * Test the ICT approval system setup
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function testSystemSetup(Request $request): JsonResponse
    {
        try {
            $results = [
                'database_connections' => $this->testDatabaseConnections(),
                'models_test' => $this->testModels(),
                'relationships_test' => $this->testRelationships(),
                'permissions_test' => $this->testPermissions($request->user()),
                'sample_data' => $this->getSampleData()
            ];

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'ICT approval system test completed'
            ]);

        } catch (\Exception $e) {
            Log::error('Error testing ICT approval system: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error testing ICT approval system',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Create test data for ICT approval system
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function createTestData(Request $request): JsonResponse
    {
        try {
            // Only allow in non-production environments
            if (app()->environment('production')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test data creation not allowed in production'
                ], 403);
            }

            DB::beginTransaction();

            // Create test users if they don't exist
            $testUsers = $this->createTestUsers();
            
            // Create test bookings
            $testBookings = $this->createTestBookings($testUsers);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'users_created' => count($testUsers),
                    'bookings_created' => count($testBookings),
                    'test_users' => $testUsers,
                    'test_bookings' => $testBookings
                ],
                'message' => 'Test data created successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating test data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error creating test data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Clean up test data
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function cleanTestData(Request $request): JsonResponse
    {
        try {
            // Only allow in non-production environments
            if (app()->environment('production')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test data cleanup not allowed in production'
                ], 403);
            }

            $deletedBookings = BookingService::where('borrower_name', 'LIKE', 'Test User%')->delete();
            $deletedUsers = User::where('email', 'LIKE', 'test%@ictapproval.test')->delete();

            return response()->json([
                'success' => true,
                'data' => [
                    'bookings_deleted' => $deletedBookings,
                    'users_deleted' => $deletedUsers
                ],
                'message' => 'Test data cleaned successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error cleaning test data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error cleaning test data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Test database connections
     */
    private function testDatabaseConnections(): array
    {
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();
            
            return [
                'status' => 'success',
                'database_name' => $connection->getDatabaseName(),
                'driver' => $connection->getDriverName(),
                'php_version' => PHP_VERSION,
                'tables' => [
                    'booking_service' => \Schema::hasTable('booking_service'),
                    'users' => \Schema::hasTable('users'),
                    'departments' => \Schema::hasTable('departments'),
                    'roles' => \Schema::hasTable('roles'),
                    'model_has_roles' => \Schema::hasTable('model_has_roles')
                ]
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test model functionality
     */
    private function testModels(): array
    {
        try {
            return [
                'BookingService' => [
                    'count' => BookingService::count(),
                    'fillable' => (new BookingService())->getFillable(),
                    'casts' => (new BookingService())->getCasts()
                ],
                'User' => [
                    'count' => User::count(),
                    'has_roles_relation' => method_exists(User::class, 'roles')
                ],
                'Department' => [
                    'count' => Department::count()
                ],
                'Role' => [
                    'count' => Role::count(),
                    'roles_list' => Role::pluck('name')->toArray()
                ]
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test model relationships
     */
    private function testRelationships(): array
    {
        try {
            // Get a sample booking to test relationships
            $booking = BookingService::with(['user', 'departmentInfo', 'approvedBy'])->first();
            
            $relationshipTests = [
                'booking_user_relation' => false,
                'booking_department_relation' => false,
                'booking_approved_by_relation' => false
            ];

            if ($booking) {
                $relationshipTests['booking_user_relation'] = $booking->user !== null;
                $relationshipTests['booking_department_relation'] = method_exists($booking, 'departmentInfo');
                $relationshipTests['booking_approved_by_relation'] = method_exists($booking, 'approvedBy');
            }

            return $relationshipTests;

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test user permissions
     */
    private function testPermissions(?User $user): array
    {
        if (!$user) {
            return [
                'status' => 'no_user',
                'message' => 'No authenticated user'
            ];
        }

        try {
            $roles = $user->roles ? $user->roles->pluck('name')->toArray() : [];
            
            return [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_roles' => $roles,
                'has_ict_permissions' => in_array('ict_officer', $roles) || in_array('admin', $roles) || in_array('ict_director', $roles),
                'can_access_ict_approval' => $user->can('access-ict-approval') ?? 'permission_system_not_implemented'
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get sample data for testing
     */
    private function getSampleData(): array
    {
        try {
            return [
                'recent_bookings' => BookingService::with(['user', 'departmentInfo'])
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function($booking) {
                        return [
                            'id' => $booking->id,
                            'request_id' => 'REQ-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
                            'borrower_name' => $booking->borrower_name,
                            'user_name' => $booking->user->name ?? 'Unknown',
                            'department' => $booking->departmentInfo->name ?? $booking->department,
                            'device_type' => $booking->device_type,
                            'status' => $booking->status,
                            'created_at' => $booking->created_at
                        ];
                    }),
                'status_breakdown' => [
                    'pending' => BookingService::where('status', 'pending')->count(),
                    'approved' => BookingService::where('status', 'approved')->count(),
                    'rejected' => BookingService::where('status', 'rejected')->count(),
                    'returned' => BookingService::where('status', 'returned')->count()
                ],
                'device_breakdown' => BookingService::selectRaw('device_type, COUNT(*) as count')
                    ->groupBy('device_type')
                    ->get()
                    ->pluck('count', 'device_type')
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create test users
     */
    private function createTestUsers(): array
    {
        $testUsers = [];
        
        // Get the staff role
        $staffRole = Role::where('name', 'staff')->first();
        $ictRole = Role::where('name', 'ict_officer')->first();
        
        // Get a test department
        $department = Department::first();

        for ($i = 1; $i <= 3; $i++) {
            $user = User::create([
                'name' => "Test User $i",
                'email' => "testuser$i@ictapproval.test",
                'phone' => "0712345678$i",
                'pf_number' => "PF12345$i",
                'password' => bcrypt('password123'),
                'department_id' => $department->id ?? null,
                'email_verified_at' => now()
            ]);

            // Assign role
            if ($staffRole) {
                $user->roles()->attach($staffRole->id);
            }

            $testUsers[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'staff'
            ];
        }

        return $testUsers;
    }

    /**
     * Create test bookings
     */
    private function createTestBookings(array $testUsers): array
    {
        $testBookings = [];
        $deviceTypes = ['laptop', 'projector', 'monitor', 'keyboard'];
        $statuses = ['pending', 'approved', 'rejected'];

        foreach ($testUsers as $index => $testUser) {
            $booking = BookingService::create([
                'user_id' => $testUser['id'],
                'booking_date' => now()->addDays($index),
                'borrower_name' => $testUser['name'],
                'device_type' => $deviceTypes[$index % count($deviceTypes)],
                'department' => 'IT Department',
                'phone_number' => '0712345678' . ($index + 1),
                'return_date' => now()->addDays($index + 7),
                'return_time' => '17:00',
                'reason' => "Test booking request $index for ICT approval system testing",
                'status' => $statuses[$index % count($statuses)]
            ]);

            $testBookings[] = [
                'id' => $booking->id,
                'request_id' => 'REQ-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
                'borrower_name' => $booking->borrower_name,
                'device_type' => $booking->device_type,
                'status' => $booking->status
            ];
        }

        return $testBookings;
    }
}
