<?php

use App\Http\Controllers\Api\v1\AuthController;

use App\Http\Controllers\Api\v1\OnboardingController;
use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\V1\UserAccessController;
use App\Http\Controllers\Api\v1\BookingServiceController;
use App\Http\Controllers\Api\v1\DeclarationController;
use App\Http\Controllers\Api\v1\BothServiceFormController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Public routes
    // Authentication routes
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('logout-all');
    Route::get('/active-sessions', [AuthController::class, 'getActiveSessions'])->name('active-sessions');
    Route::post('/revoke-session', [AuthController::class, 'revokeSession'])->name('revoke-session');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        
        // Load the role relationship
        $user->load('role');
        
        // Return user with role information
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'pf_number' => $user->pf_number,
            'staff_name' => $user->staff_name,
            'role_id' => $user->role_id,
            'role' => $user->role ? $user->role->name : null,
            'role_name' => $user->role ? $user->role->name : null,
            'needs_onboarding' => $user->needsOnboarding(),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
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
        Route::get('/users', [AdminController::class, 'getUsers'])->name('admin.users.index');
        Route::get('/users/{userId}', [AdminController::class, 'getUserDetails'])->name('admin.users.show');
        Route::post('/users/reset-onboarding', [AdminController::class, 'resetUserOnboarding'])->name('admin.users.reset-onboarding');
        Route::post('/users/bulk-reset-onboarding', [AdminController::class, 'bulkResetOnboarding'])->name('admin.users.bulk-reset-onboarding');
        Route::get('/onboarding/stats', [AdminController::class, 'getOnboardingStats'])->name('admin.onboarding.stats');
    });



    // User Access Request routes (v1)
    Route::prefix('v1')->group(function () {
        // User Access Request CRUD operations
        Route::apiResource('user-access', UserAccessController::class);
        
        // Combined Access Request route
        Route::post('combined-access', [UserAccessController::class, 'store'])->name('combined-access.store');
        
        // Additional utility routes
        Route::get('departments', [UserAccessController::class, 'getDepartments']);
        Route::post('check-signature', [UserAccessController::class, 'checkSignature']);
        Route::get('user-access/pending-status', [UserAccessController::class, 'checkPendingRequests']);
    });

    // Booking Service routes
    Route::prefix('booking-service')->group(function () {
        // CRUD operations
        Route::apiResource('bookings', BookingServiceController::class);
        
        // Utility routes
        Route::get('device-types', [BookingServiceController::class, 'getDeviceTypes'])->name('booking-service.device-types');
        Route::get('departments', [BookingServiceController::class, 'getDepartments'])->name('booking-service.departments');
        Route::get('statistics', [BookingServiceController::class, 'getStatistics'])->name('booking-service.statistics');
        

        
        // Admin actions
        Route::post('bookings/{bookingService}/approve', [BookingServiceController::class, 'approve'])->name('booking-service.approve');
        Route::post('bookings/{bookingService}/reject', [BookingServiceController::class, 'reject'])->name('booking-service.reject');
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
            ->middleware('both.service.role:hod')
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
            ->middleware('both.service.role:hod_it')
            ->name('both-service-form.approve.hod-it');
        Route::post('/{id}/approve/ict-officer', [BothServiceFormController::class, 'approveAsICTOfficer'])
            ->middleware('both.service.role:ict_officer')
            ->name('both-service-form.approve.ict-officer');
    });

    // Role Management routes (Admin only)
    Route::prefix('roles')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\v1\RoleController::class, 'index'])->name('roles.index');
        Route::post('/', [\App\Http\Controllers\Api\v1\RoleController::class, 'store'])->name('roles.store');
        Route::get('/statistics', [\App\Http\Controllers\Api\v1\RoleController::class, 'statistics'])->name('roles.statistics');
        Route::get('/permissions', [\App\Http\Controllers\Api\v1\RoleController::class, 'permissions'])->name('roles.permissions');
        Route::get('/{role}', [\App\Http\Controllers\Api\v1\RoleController::class, 'show'])->name('roles.show');
        Route::put('/{role}', [\App\Http\Controllers\Api\v1\RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{role}', [\App\Http\Controllers\Api\v1\RoleController::class, 'destroy'])->name('roles.destroy');
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
        Route::delete('/{department}/remove', [\App\Http\Controllers\Api\v1\DepartmentHodController::class, 'removeHod'])->name('department-hod.remove');
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
