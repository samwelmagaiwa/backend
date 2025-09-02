<?php

use App\Http\Controllers\Api\v1\AuthController;

use App\Http\Controllers\Api\v1\OnboardingController;
use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\V1\UserAccessController;
use App\Http\Controllers\Api\v1\BookingServiceController;
use App\Http\Controllers\Api\v1\DeclarationController;
use App\Http\Controllers\Api\v1\BothServiceFormController;
use App\Http\Controllers\Api\v1\AdminUserController;
use App\Http\Controllers\Api\v1\AdminDepartmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('logout-all');
    Route::get('/active-sessions', [AuthController::class, 'getActiveSessions'])->name('active-sessions');
    Route::post('/revoke-session', [AuthController::class, 'revokeSession'])->name('revoke-session');
    Route::get('/role-redirect', [AuthController::class, 'getRoleBasedRedirect'])->name('role-redirect');
Route::post('/register', [AuthController::class, 'register'])->name('register');

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
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('logout-all');
    Route::get('/sessions', [AuthController::class, 'getActiveSessions'])->name('sessions');
    Route::post('/sessions/revoke', [AuthController::class, 'revokeSession'])->name('sessions.revoke');
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
        Route::get('/users', [AdminController::class, 'getUsers'])->name('admin.users.index');
        Route::get('/users/{userId}', [AdminController::class, 'getUserDetails'])->name('admin.users.show');
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