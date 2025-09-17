<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\SecurityTestController;
use App\Http\Controllers\Api\v1\OnboardingController;
use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\v1\UserAccessController;
use App\Http\Controllers\Api\v1\BookingServiceController;
use App\Http\Controllers\Api\v1\ICTApprovalController;
use App\Http\Controllers\Api\v1\DeclarationController;
use App\Http\Controllers\Api\v1\BothServiceFormController;
use App\Http\Controllers\Api\v1\AdminUserController;
use App\Http\Controllers\Api\v1\AdminDepartmentController;
use App\Http\Controllers\Api\v1\DeviceInventoryController;
use App\Http\Controllers\Api\v1\HodCombinedAccessController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/api/documentation');
});

// Simple test route - should work if routing is functioning
Route::get('/test-simple', function () {
    return response()->json(['message' => 'Test route working', 'timestamp' => now()]);
});

// Public routes
    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'database' => 'checking...'
        ]);
    });
    
    // Detailed health check with database test
    Route::get('/health/detailed', function () {
        $dbStatus = 'ok';
        $dbError = null;
        
        try {
            \DB::connection()->getPdo();
            \DB::table('users')->count(); // Test a simple query
        } catch (\Exception $e) {
            $dbStatus = 'error';
            $dbError = $e->getMessage();
        }
        
        return response()->json([
            'status' => $dbStatus === 'ok' ? 'ok' : 'error',
            'timestamp' => now()->toISOString(),
            'database' => [
                'status' => $dbStatus,
                'error' => $dbError
            ],
            'environment' => app()->environment(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version()
        ]);
    });
    
    // Authentication routes
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register')->name('register');
    
    // Security Test Routes (for testing security implementations)
    Route::prefix('security-test')->group(function () {
        Route::get('/test', [SecurityTestController::class, 'test'])->name('security-test.basic');
        Route::get('/health', [SecurityTestController::class, 'healthCheck'])->name('security-test.health');
        Route::get('/rate-limit', [SecurityTestController::class, 'rateLimitTest'])->name('security-test.rate-limit');
        Route::post('/sanitization', [SecurityTestController::class, 'sanitizationTest'])->name('security-test.sanitization');
        Route::post('/xss', [SecurityTestController::class, 'xssTest'])->name('security-test.xss');
    });

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        
        // Load the roles relationship (new system)
        $user->load('roles', 'department');
        
        // Get primary role for consistent role handling
        $primaryRole = $user->getPrimaryRoleName();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        // Return user with role information using new system
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'pf_number' => $user->pf_number,
            'staff_name' => $user->staff_name,
            'department_id' => $user->department_id,
            'department' => $user->department ? [
                'id' => $user->department->id,
                'name' => $user->department->name,
                'code' => $user->department->code,
                'display_name' => $user->department->getFullNameAttribute()
            ] : null,
            'is_active' => $user->is_active ?? true,
            'role' => $primaryRole, // Normalized role field
            'role_name' => $primaryRole, // For backward compatibility
            'primary_role' => $primaryRole, // Explicit primary role
            'roles' => $userRoles, // Array of role names
            'role_objects' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => ucwords(str_replace('_', ' ', $role->name))
                ];
            }),
            'display_roles' => $user->getDisplayRoleNames(),
            'permissions' => $user->getAllPermissions(),
            'needs_onboarding' => $user->needsOnboarding(),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    });
    
    // Authentication routes
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('throttle:sensitive')->name('logout');
    Route::post('/logout-all', [AuthController::class, 'logoutAll'])->middleware('throttle:sensitive')->name('logout-all');
    Route::get('/sessions', [AuthController::class, 'getActiveSessions'])->middleware('throttle:api')->name('sessions');
    Route::post('/sessions/revoke', [AuthController::class, 'revokeSession'])->middleware('throttle:sensitive')->name('sessions.revoke');
    Route::get('/current-user', [AuthController::class, 'getCurrentUser'])->name('current-user');
    Route::get('/role-redirect', [AuthController::class, 'getRoleBasedRedirect'])->name('role-redirect');

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

    // Declaration routes
    Route::prefix('declaration')->group(function () {
        Route::get('/departments', [DeclarationController::class, 'getDepartments'])->name('declaration.departments');
        Route::post('/submit', [DeclarationController::class, 'store'])->name('declaration.submit');
        Route::get('/show', [DeclarationController::class, 'show'])->name('declaration.show');
        Route::post('/check-pf-number', [DeclarationController::class, 'checkPfNumber'])->name('declaration.check-pf-number');
        Route::get('/all', [DeclarationController::class, 'index'])->name('declaration.index'); // Admin only
    });

    // Admin routes (Admin only)
    Route::prefix('admin')->group(function () {
        // Legacy admin routes
        Route::post('/users/reset-onboarding', [AdminController::class, 'resetUserOnboarding'])->name('admin.users.reset-onboarding');
        Route::post('/users/bulk-reset-onboarding', [AdminController::class, 'bulkResetOnboarding'])->name('admin.users.bulk-reset-onboarding');
        Route::get('/onboarding/stats', [AdminController::class, 'getOnboardingStats'])->name('admin.onboarding.stats');
        
        // New comprehensive user management routes
        Route::prefix('users')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('admin.users.index');
            Route::post('/', [AdminUserController::class, 'store'])->name('admin.users.store');
            Route::get('/roles', [AdminUserController::class, 'getRoles'])->name('admin.users.roles');
            Route::get('/departments', [AdminUserController::class, 'getDepartments'])->name('admin.users.departments');
            Route::get('/create-form-data', [AdminUserController::class, 'getCreateFormData'])->name('admin.users.create-form-data');
            Route::post('/validate', [AdminUserController::class, 'validateUserData'])->name('admin.users.validate');
            Route::get('/statistics', [AdminUserController::class, 'getStatistics'])->name('admin.users.statistics');
            Route::get('/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
            Route::put('/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
            Route::patch('/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        });
        
        // Department management routes
        Route::prefix('departments')->group(function () {
            Route::get('/', [AdminDepartmentController::class, 'index'])->name('admin.departments.index');
            Route::post('/', [AdminDepartmentController::class, 'store'])->name('admin.departments.store');
            Route::get('/create-form-data', [AdminDepartmentController::class, 'getCreateFormData'])->name('admin.departments.create-form-data');
            Route::get('/eligible-hods', [AdminDepartmentController::class, 'getEligibleHods'])->name('admin.departments.eligible-hods');
            Route::get('/eligible-divisional-directors', [AdminDepartmentController::class, 'getEligibleDivisionalDirectors'])->name('admin.departments.eligible-divisional-directors');
            Route::get('/{department}', [AdminDepartmentController::class, 'show'])->name('admin.departments.show');
            Route::put('/{department}', [AdminDepartmentController::class, 'update'])->name('admin.departments.update');
            Route::delete('/{department}', [AdminDepartmentController::class, 'destroy'])->name('admin.departments.destroy');
            Route::patch('/{department}/toggle-status', [AdminDepartmentController::class, 'toggleStatus'])->name('admin.departments.toggle-status');
        });
        
        // Legacy user management routes (keep for backward compatibility)
        Route::prefix('user-management')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('admin.user-management.index');
            Route::post('/', [AdminUserController::class, 'store'])->name('admin.user-management.store');
            Route::get('/roles', [AdminUserController::class, 'getRoles'])->name('admin.user-management.roles');
            Route::get('/statistics', [AdminUserController::class, 'getStatistics'])->name('admin.user-management.statistics');
            Route::get('/{user}', [AdminUserController::class, 'show'])->name('admin.user-management.show');
            Route::put('/{user}', [AdminUserController::class, 'update'])->name('admin.user-management.update');
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('admin.user-management.destroy');
            Route::post('/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('admin.user-management.toggle-status');
        });
    });



    // User Access Request routes (v1)
    Route::prefix('v1')->group(function () {
        // Specific utility routes MUST come before apiResource to avoid parameter capture
        Route::get('user-access/pending-status', [UserAccessController::class, 'checkPendingRequests']);
        
        // User Access Request CRUD operations
        Route::apiResource('user-access', UserAccessController::class);
        
        // POST route with method spoofing for updates (to handle multipart/form-data)
        Route::post('user-access/{userAccess}', [UserAccessController::class, 'update'])
            ->name('user-access.update-multipart');
        
        // Combined Access Request route
        Route::post('combined-access', [UserAccessController::class, 'store'])->name('combined-access.store');
        
        // Additional utility routes
        Route::get('departments', [UserAccessController::class, 'getDepartments']);
        Route::post('check-signature', [UserAccessController::class, 'checkSignature']);
    });

    // Request Status routes (for staff users to view their requests)
    Route::prefix('request-status')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\v1\RequestStatusController::class, 'index'])->name('request-status.index');
        Route::get('/details', [\App\Http\Controllers\Api\v1\RequestStatusController::class, 'show'])->name('request-status.show');
        Route::get('/statistics', [\App\Http\Controllers\Api\v1\RequestStatusController::class, 'statistics'])->name('request-status.statistics');
        Route::get('/types', [\App\Http\Controllers\Api\v1\RequestStatusController::class, 'getRequestTypes'])->name('request-status.types');
        Route::get('/statuses', [\App\Http\Controllers\Api\v1\RequestStatusController::class, 'getStatusOptions'])->name('request-status.statuses');
        Route::get('/debug', [\App\Http\Controllers\Api\v1\RequestStatusController::class, 'debug'])->name('request-status.debug');
    });

    // Booking Service routes
    Route::prefix('booking-service')->group(function () {
        // CRUD operations
        Route::apiResource('bookings', BookingServiceController::class);
        
        // Edit rejected booking request route
        Route::put('bookings/{bookingId}/edit-rejected', [BookingServiceController::class, 'updateRejectedRequest'])->name('booking-service.edit-rejected');
        
        // Utility routes
        Route::get('device-types', [BookingServiceController::class, 'getDeviceTypes'])->name('booking-service.device-types');
        Route::get('departments', [BookingServiceController::class, 'getDepartments'])->name('booking-service.departments');
        Route::get('statistics', [BookingServiceController::class, 'getStatistics'])->name('booking-service.statistics');
        Route::get('debug-departments', [BookingServiceController::class, 'debugDepartments'])->name('booking-service.debug-departments');
        Route::get('debug-assessment-schema', [BookingServiceController::class, 'debugAssessmentSchema'])->name('booking-service.debug-assessment-schema');
        Route::post('seed-departments', [BookingServiceController::class, 'seedDepartments'])->name('booking-service.seed-departments');
        

        
        // Device availability checking
        Route::get('devices/{deviceInventoryId}/availability', [BookingServiceController::class, 'checkDeviceAvailability'])->name('booking-service.device-availability');
        Route::get('devices/{deviceInventoryId}/bookings', [BookingServiceController::class, 'getDeviceBookings'])->name('booking-service.device-bookings');
        
        // Pending request checking
        Route::get('check-pending-requests', [BookingServiceController::class, 'checkPendingRequests'])->name('booking-service.check-pending-requests');
        Route::get('can-submit-new-request', [BookingServiceController::class, 'canUserSubmitNewRequest'])->name('booking-service.can-submit-new-request');
        
        // Admin actions
        Route::post('bookings/{bookingService}/approve', [BookingServiceController::class, 'approve'])->name('booking-service.approve');
        Route::post('bookings/{bookingService}/reject', [BookingServiceController::class, 'reject'])->name('booking-service.reject');
        
        // ICT Officer actions
        Route::get('ict-approval-requests', [BookingServiceController::class, 'getIctApprovalRequests'])
            ->middleware('role:ict_officer,admin,ict_director')
            ->name('booking-service.ict-approval-requests');
        // Alias for frontend compatibility
        Route::get('ict-pending-approvals', [BookingServiceController::class, 'getIctApprovalRequests'])
            ->middleware('role:ict_officer,admin,ict_director')
            ->name('booking-service.ict-pending-approvals');
        Route::post('bookings/{bookingService}/ict-approve', [BookingServiceController::class, 'ictApprove'])
            ->middleware('role:ict_officer,admin,ict_director')
            ->name('booking-service.ict-approve');
        Route::post('bookings/{bookingService}/ict-reject', [BookingServiceController::class, 'ictReject'])
            ->middleware('role:ict_officer,admin,ict_director')
            ->name('booking-service.ict-reject');
        
        // Device condition assessment routes
        Route::post('bookings/{bookingService}/assessment/issuing', [BookingServiceController::class, 'saveIssuingAssessment'])
            ->middleware('role:ict_officer,admin,ict_director')
            ->name('booking-service.assessment.issuing');
        Route::post('bookings/{bookingService}/assessment/receiving', [BookingServiceController::class, 'saveReceivingAssessment'])
            ->middleware('role:ict_officer,admin,ict_director')
            ->name('booking-service.assessment.receiving');
    });

    // ICT Approval routes (ICT Officer only)
    Route::prefix('ict-approval')->middleware('role:ict_officer,admin,ict_director')->group(function () {
        // Debug endpoint (must be before parameterized routes)
        Route::get('debug', [ICTApprovalController::class, 'debugICTApprovalSystem'])->name('ict-approval.debug');
        
        // Statistics (must be before parameterized routes)
        Route::get('device-requests/statistics', [ICTApprovalController::class, 'getDeviceBorrowingStatistics'])->name('ict-approval.statistics');
        
        // Device borrowing requests management
        Route::get('device-requests', [ICTApprovalController::class, 'getDeviceBorrowingRequests'])->name('ict-approval.device-requests');
        Route::get('device-requests/{requestId}', [ICTApprovalController::class, 'getDeviceBorrowingRequestDetails'])->name('ict-approval.device-request-details');
        
        // Approval/rejection actions
        Route::post('device-requests/{requestId}/approve', [ICTApprovalController::class, 'approveDeviceBorrowingRequest'])->name('ict-approval.approve');
        Route::post('device-requests/{requestId}/reject', [ICTApprovalController::class, 'rejectDeviceBorrowingRequest'])->name('ict-approval.reject');
        Route::delete('device-requests/{requestId}', [ICTApprovalController::class, 'deleteDeviceBorrowingRequest'])->name('ict-approval.delete');
        Route::post('device-requests/bulk-delete', [ICTApprovalController::class, 'bulkDeleteDeviceBorrowingRequests'])->name('ict-approval.bulk-delete');
        
        // User details auto-capture
        Route::post('device-requests/{bookingId}/link-user', [ICTApprovalController::class, 'linkUserDetailsToBooking'])->name('ict-approval.link-user');
        
        // Device condition assessment routes
        Route::post('device-requests/{requestId}/assessment/issuing', [ICTApprovalController::class, 'saveIssuingAssessment'])->name('ict-approval.assessment.issuing');
        Route::post('device-requests/{requestId}/assessment/receiving', [ICTApprovalController::class, 'saveReceivingAssessment'])->name('ict-approval.assessment.receiving');
    });

    // Both Service Form routes (HOD Dashboard)
    Route::prefix('both-service-form')->middleware('both.service.role')->group(function () {
        // Basic CRUD operations
        Route::get('/', [BothServiceFormController::class, 'index'])->name('both-service-form.index');
        Route::post('/', [BothServiceFormController::class, 'store'])->name('both-service-form.store');
        Route::get('/{id}', [BothServiceFormController::class, 'show'])->name('both-service-form.show');
        
        // Table data with specific columns
        Route::get('/table/data', [BothServiceFormController::class, 'getTableData'])->name('both-service-form.table-data');
        
        // HOD specific routes
        Route::get('/{id}/hod-view', [BothServiceFormController::class, 'getFormForHOD'])
            ->middleware('both.service.role:hod')
            ->name('both-service-form.hod-view');
        Route::post('/{id}/hod-submit', [BothServiceFormController::class, 'hodSubmitToNextStage'])
            ->middleware('both.service.role:hod')
            ->name('both-service-form.hod-submit');
        
        // Utility routes
        Route::get('/user/info', [BothServiceFormController::class, 'getUserInfo'])->name('both-service-form.user-info');
        Route::get('/departments/list', [BothServiceFormController::class, 'getDepartments'])->name('both-service-form.departments');

        Route::get('/debug-hod', [BothServiceFormController::class, 'debugHodAssignments'])->name('both-service-form.debug-hod');
        Route::get('/{id}/export-pdf', [BothServiceFormController::class, 'exportPdf'])->name('both-service-form.export-pdf');
        
        // Personal information routes from user_access table
        Route::get('/user-access/{userAccessId}/personal-info', [BothServiceFormController::class, 'getPersonalInfoFromUserAccess'])
            ->name('both-service-form.personal-info');
        Route::get('/hod/user-access-requests', [BothServiceFormController::class, 'getUserAccessRequestsForHOD'])
            ->middleware('both.service.role:hod,divisional_director')
            ->name('both-service-form.hod.user-access-requests');
        
        // Role-based approval routes with specific role requirements
        Route::post('/{id}/approve/hod', [BothServiceFormController::class, 'approveAsHOD'])
            ->middleware('both.service.role:hod')
            ->name('both-service-form.approve.hod');
        Route::post('/{id}/approve/divisional-director', [BothServiceFormController::class, 'approveAsDivisionalDirector'])
            ->middleware('both.service.role:divisional_director')
            ->name('both-service-form.approve.divisional-director');
        Route::post('/{id}/approve/dict', [BothServiceFormController::class, 'approveAsDICT'])
            ->middleware('both.service.role:dict')
            ->name('both-service-form.approve.dict');
        Route::post('/{id}/approve/hod-it', [BothServiceFormController::class, 'approveAsHODIT'])

            ->name('both-service-form.approve.hod-it');
        Route::post('/{id}/approve/ict-officer', [BothServiceFormController::class, 'approveAsICTOfficer'])
            ->middleware('both.service.role:ict_officer')
            ->name('both-service-form.approve.ict-officer');
    });



    // User Role Management routes (Admin only)
    Route::prefix('user-roles')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\v1\UserRoleController::class, 'index'])->name('user-roles.index');
        Route::get('/statistics', [\App\Http\Controllers\Api\v1\UserRoleController::class, 'statistics'])->name('user-roles.statistics');
        Route::post('/{user}/assign', [\App\Http\Controllers\Api\v1\UserRoleController::class, 'assignRoles'])->name('user-roles.assign');
        Route::delete('/{user}/roles/{role}', [\App\Http\Controllers\Api\v1\UserRoleController::class, 'removeRole'])->name('user-roles.remove');
        Route::get('/{user}/history', [\App\Http\Controllers\Api\v1\UserRoleController::class, 'roleHistory'])->name('user-roles.history');
    });

    // Department HOD Management routes (Admin only)
    Route::prefix('department-hod')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\v1\DepartmentHodController::class, 'index'])->name('department-hod.index');
        Route::get('/eligible-hods', [\App\Http\Controllers\Api\v1\DepartmentHodController::class, 'eligibleHods'])->name('department-hod.eligible');
        Route::get('/statistics', [\App\Http\Controllers\Api\v1\DepartmentHodController::class, 'statistics'])->name('department-hod.statistics');
        Route::post('/{department}/assign', [\App\Http\Controllers\Api\v1\DepartmentHodController::class, 'assignHod'])->name('department-hod.assign');
        Route::put('/{department}/update', [\App\Http\Controllers\Api\v1\DepartmentHodController::class, 'updateHod'])->name('department-hod.update');
        Route::get('/{department}/details', [\App\Http\Controllers\Api\v1\DepartmentHodController::class, 'getHodDetails'])->name('department-hod.details');
        Route::delete('/{department}/remove', [\App\Http\Controllers\Api\v1\DepartmentHodController::class, 'removeHod'])->name('department-hod.remove');
        Route::delete('/{department}/delete', [\App\Http\Controllers\Api\v1\DepartmentHodController::class, 'deleteHod'])->name('department-hod.delete');
    });

    // Device Inventory Management routes (Admin only)
    Route::prefix('device-inventory')->group(function () {
        Route::get('/', [DeviceInventoryController::class, 'index'])->name('device-inventory.index');
        Route::post('/', [DeviceInventoryController::class, 'store'])->name('device-inventory.store');
        Route::get('/available', [DeviceInventoryController::class, 'getAvailableDevices'])->name('device-inventory.available');
        Route::get('/statistics', [DeviceInventoryController::class, 'getStatistics'])->name('device-inventory.statistics');
        Route::post('/fix-quantities', [DeviceInventoryController::class, 'fixQuantities'])->name('device-inventory.fix-quantities');
        Route::get('/{deviceInventory}', [DeviceInventoryController::class, 'show'])->name('device-inventory.show');
        Route::put('/{deviceInventory}', [DeviceInventoryController::class, 'update'])->name('device-inventory.update');
        Route::delete('/{deviceInventory}', [DeviceInventoryController::class, 'destroy'])->name('device-inventory.destroy');
    });

    // User Dashboard routes (Staff only)
    Route::prefix('user')->group(function () {
        Route::get('/dashboard-stats', [\App\Http\Controllers\Api\v1\UserDashboardController::class, 'getDashboardStats'])->name('user.dashboard-stats');
        Route::get('/request-status-breakdown', [\App\Http\Controllers\Api\v1\UserDashboardController::class, 'getRequestStatusBreakdown'])->name('user.request-status-breakdown');
        Route::get('/recent-activity', [\App\Http\Controllers\Api\v1\UserDashboardController::class, 'getRecentActivity'])->name('user.recent-activity');
    });

    // HOD Combined Access Request routes (HOD only) - LEGACY
    Route::prefix('hod')->middleware('role:head_of_department,divisional_director,ict_director,admin')->group(function () {
        Route::get('combined-access-requests', [HodCombinedAccessController::class, 'index'])
            ->name('hod.combined-access-requests.index');
        Route::get('combined-access-requests/statistics', [HodCombinedAccessController::class, 'statistics'])
            ->name('hod.combined-access-requests.statistics');
        Route::get('combined-access-requests/{id}', [HodCombinedAccessController::class, 'show'])
            ->name('hod.combined-access-requests.show');
        Route::post('combined-access-requests/{id}/approve', [HodCombinedAccessController::class, 'updateApproval'])
            ->name('hod.combined-access-requests.approve');
        Route::post('combined-access-requests/{id}/cancel', [HodCombinedAccessController::class, 'cancel'])
            ->name('hod.combined-access-requests.cancel');
    });

    // ========================================
    // NEW COMPLETE USER ACCESS WORKFLOW ROUTES
    // ========================================
    
    // User Access Workflow routes - Complete system for all stakeholders
    Route::prefix('user-access-workflow')->group(function () {
        // Basic CRUD operations
        Route::get('/', [\App\Http\Controllers\Api\UserAccessWorkflowController::class, 'index'])
            ->name('user-access-workflow.index');
        Route::post('/', [\App\Http\Controllers\Api\UserAccessWorkflowController::class, 'store'])
            ->name('user-access-workflow.store');
        Route::get('/{userAccess}', [\App\Http\Controllers\Api\UserAccessWorkflowController::class, 'show'])
            ->name('user-access-workflow.show');
        Route::put('/{userAccess}', [\App\Http\Controllers\Api\UserAccessWorkflowController::class, 'update'])
            ->name('user-access-workflow.update');
        
        // Utility routes
        Route::get('/options/form-data', [\App\Http\Controllers\Api\UserAccessWorkflowController::class, 'getFormOptions'])
            ->name('user-access-workflow.form-options');
        Route::get('/statistics/dashboard', [\App\Http\Controllers\Api\UserAccessWorkflowController::class, 'getStatistics'])
            ->name('user-access-workflow.statistics');
        Route::post('/export/requests', [\App\Http\Controllers\Api\UserAccessWorkflowController::class, 'export'])
            ->name('user-access-workflow.export');
        Route::post('/{userAccess}/cancel', [\App\Http\Controllers\Api\UserAccessWorkflowController::class, 'cancel'])
            ->name('user-access-workflow.cancel');
        
        // Approval workflow routes - Role-based access control
        Route::post('/{userAccess}/approve/hod', [\App\Http\Controllers\Api\UserAccessWorkflowController::class, 'processHodApproval'])
            ->middleware('role:head_of_department')
            ->name('user-access-workflow.approve.hod');
        Route::post('/{userAccess}/approve/divisional', [\App\Http\Controllers\Api\UserAccessWorkflowController::class, 'processDivisionalApproval'])
            ->middleware('role:divisional_director')
            ->name('user-access-workflow.approve.divisional');
        Route::post('/{userAccess}/approve/ict-director', [\App\Http\Controllers\Api\UserAccessWorkflowController::class, 'processIctDirectorApproval'])
            ->middleware('role:ict_director')
            ->name('user-access-workflow.approve.ict-director');
        Route::post('/{userAccess}/approve/head-it', [\App\Http\Controllers\Api\UserAccessWorkflowController::class, 'processHeadItApproval'])
            ->middleware('role:head_it')
            ->name('user-access-workflow.approve.head-it');
        Route::post('/{userAccess}/implement/ict-officer', [\App\Http\Controllers\Api\UserAccessWorkflowController::class, 'processIctOfficerImplementation'])
            ->middleware('role:ict_officer')
            ->name('user-access-workflow.implement.ict-officer');
    });

    // User Profile routes (for form auto-population)
    Route::prefix('profile')->group(function () {
        Route::get('/current', [\App\Http\Controllers\Api\v1\UserProfileController::class, 'getCurrentUserProfile'])->name('profile.current');
        Route::put('/current', [\App\Http\Controllers\Api\v1\UserProfileController::class, 'updateCurrentUserProfile'])->name('profile.update');
        Route::post('/lookup-pf', [\App\Http\Controllers\Api\v1\UserProfileController::class, 'getUserByPfNumber'])->name('profile.lookup-pf');
        Route::post('/check-pf', [\App\Http\Controllers\Api\v1\UserProfileController::class, 'checkPfNumberExists'])->name('profile.check-pf');
        Route::get('/departments', [\App\Http\Controllers\Api\v1\UserProfileController::class, 'getDepartments'])->name('profile.departments');
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