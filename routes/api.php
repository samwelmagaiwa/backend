<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\JeevaAccessRequestController;
use App\Http\Controllers\Api\v1\ModuleAccessRequestController;
use App\Http\Controllers\Api\v1\OnboardingController;
use App\Http\Controllers\Api\v1\AdminController;
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
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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
});
