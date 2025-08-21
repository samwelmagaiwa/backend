<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\JeevaAccessRequestController;
use App\Http\Controllers\Api\v1\ModuleAccessRequestController;
use App\Http\Controllers\Api\v1\OnboardingController;
use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\V1\UserAccessController;
use App\Http\Controllers\Api\v1\BookingServiceController;
// COMMENTED OUT: Individual form controllers - now using Combined Access Form only
// use App\Http\Controllers\Api\v1\UserJeevaFormController;
// use App\Http\Controllers\Api\v1\UserWellsoftFormController;
// use App\Http\Controllers\Api\v1\UserInternetAccessFormController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Authentication routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('logout-all');
    Route::get('/sessions', [AuthController::class, 'getActiveSessions'])->name('sessions');
    Route::post('/sessions/revoke', [AuthController::class, 'revokeSession'])->name('sessions.revoke');

    // Onboarding routes
    Route::prefix('onboarding')->group(function () {
        Route::get('/status', [OnboardingController::class, 'getStatus'])->name('onboarding.status');
        Route::post('/accept-terms', [OnboardingController::class, 'acceptTerms'])->name('onboarding.accept-terms');
        Route::post('/accept-ict-policy', [OnboardingController::class, 'acceptIctPolicy'])->name('onboarding.accept-ict-policy');
        Route::post('/submit-declaration', [OnboardingController::class, 'submitDeclaration'])->name('onboarding.submit-declaration');
        Route::post('/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
        Route::post('/update-step', [OnboardingController::class, 'updateStep'])->name('onboarding.update-step');
        Route::post('/reset', [OnboardingController::class, 'reset'])->name('onboarding.reset'); // Admin only
    });

    // Admin routes (Admin only)
    Route::prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'getUsers'])->name('admin.users.index');
        Route::get('/users/{userId}', [AdminController::class, 'getUserDetails'])->name('admin.users.show');
        Route::post('/users/reset-onboarding', [AdminController::class, 'resetUserOnboarding'])->name('admin.users.reset-onboarding');
        Route::post('/users/bulk-reset-onboarding', [AdminController::class, 'bulkResetOnboarding'])->name('admin.users.bulk-reset-onboarding');
        Route::get('/onboarding/stats', [AdminController::class, 'getOnboardingStats'])->name('admin.onboarding.stats');
    });

    Route::apiResource('jeeva-access-requests', JeevaAccessRequestController::class);
    Route::apiResource('module-access-requests', ModuleAccessRequestController::class);
    Route::get('/user-info', [ModuleAccessRequestController::class, 'userInfo'])->name('user-info');
    Route::get('/jeeva-access-requests/pdf', [JeevaAccessRequestController::class, 'exportPdf'])->name('jeeva-access-requests.pdf');

    // User Access Request routes (v1)
    Route::prefix('v1')->group(function () {
        // User Access Request CRUD operations
        Route::apiResource('user-access', UserAccessController::class);
        
        // Combined Access Request route
        Route::post('combined-access', [UserAccessController::class, 'store'])->name('combined-access.store');
        
        // Additional utility routes
        Route::get('departments', [UserAccessController::class, 'getDepartments']);
        Route::post('check-signature', [UserAccessController::class, 'checkSignature']);
    });

    // Booking Service routes
    Route::prefix('booking-service')->group(function () {
        // CRUD operations
        Route::apiResource('bookings', BookingServiceController::class);
        
        // Utility routes
        Route::get('device-types', [BookingServiceController::class, 'getDeviceTypes'])->name('booking-service.device-types');
        Route::get('departments', [BookingServiceController::class, 'getDepartments'])->name('booking-service.departments');
        Route::get('statistics', [BookingServiceController::class, 'getStatistics'])->name('booking-service.statistics');
        
        // Test route for debugging
        Route::post('test-validation', function(\Illuminate\Http\Request $request) {
            \Log::info('Test validation endpoint called', $request->all());
            
            try {
                $validated = $request->validate([
                    'booking_date' => 'required|date',
                    'borrower_name' => 'required|string|max:255',
                    'device_type' => 'required|string',
                    'department' => 'required|integer',
                    'phone_number' => 'required|string',
                    'return_date' => 'required|date',
                    'return_time' => 'required|string',
                    'reason' => 'required|string|min:10',
                    'signature' => 'required|file'
                ]);
                
                \Log::info('Basic validation passed', $validated);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Basic validation passed',
                    'data' => $validated
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Basic validation failed', $e->errors());
                
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                    'message' => 'Basic validation failed'
                ], 422);
            }
        })->name('booking-service.test-validation');
        
        // Admin actions
        Route::post('bookings/{bookingService}/approve', [BookingServiceController::class, 'approve'])->name('booking-service.approve');
        Route::post('bookings/{bookingService}/reject', [BookingServiceController::class, 'reject'])->name('booking-service.reject');
    });

    // COMMENTED OUT: Individual form routes - now using Combined Access Form only
    /*
    // User Jeeva Form routes (Staff only)
    Route::prefix('user-jeeva')->group(function () {
        Route::post('submit', [UserJeevaFormController::class, 'store'])->name('user-jeeva.submit');
        Route::get('requests', [UserJeevaFormController::class, 'index'])->name('user-jeeva.index');
        Route::get('requests/{userAccess}', [UserJeevaFormController::class, 'show'])->name('user-jeeva.show');
        Route::get('departments', [UserJeevaFormController::class, 'getDepartments'])->name('user-jeeva.departments');
        Route::post('check-signature', [UserJeevaFormController::class, 'checkSignature'])->name('user-jeeva.check-signature');
    });

    // User Wellsoft Form routes (Staff only)
    Route::prefix('user-wellsoft')->group(function () {
        Route::post('submit', [UserWellsoftFormController::class, 'store'])->name('user-wellsoft.submit');
        Route::get('requests', [UserWellsoftFormController::class, 'index'])->name('user-wellsoft.index');
        Route::get('requests/{userAccess}', [UserWellsoftFormController::class, 'show'])->name('user-wellsoft.show');
        Route::get('departments', [UserWellsoftFormController::class, 'getDepartments'])->name('user-wellsoft.departments');
        Route::post('check-signature', [UserWellsoftFormController::class, 'checkSignature'])->name('user-wellsoft.check-signature');
    });

    // User Internet Access Form routes (Staff only)
    Route::prefix('user-internet-access')->group(function () {
        Route::post('submit', [UserInternetAccessFormController::class, 'store'])->name('user-internet-access.submit');
        Route::get('requests', [UserInternetAccessFormController::class, 'index'])->name('user-internet-access.index');
        Route::get('requests/{userAccess}', [UserInternetAccessFormController::class, 'show'])->name('user-internet-access.show');
        Route::get('departments', [UserInternetAccessFormController::class, 'getDepartments'])->name('user-internet-access.departments');
        Route::post('check-signature', [UserInternetAccessFormController::class, 'checkSignature'])->name('user-internet-access.check-signature');
    });
    */
});
