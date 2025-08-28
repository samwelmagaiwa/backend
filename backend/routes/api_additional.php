<?php

// Additional routes for User Role Management
// Add these routes to your main api.php file

// Additional User Role Management routes (Admin only)
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('user-roles')->group(function () {
        Route::get('/departments', [\App\Http\Controllers\Api\v1\UserRoleController::class, 'getDepartments'])->name('user-roles.departments');
        Route::post('/create-user', [\App\Http\Controllers\Api\v1\UserRoleController::class, 'createUser'])->name('user-roles.create-user');
    });
});