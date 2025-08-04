<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\JeevaAccessRequestController;
use App\Http\Controllers\Api\v1\ModuleAccessRequestController;
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

    Route::apiResource('jeeva-access-requests', JeevaAccessRequestController::class);
    Route::apiResource('module-access-requests', ModuleAccessRequestController::class);
    Route::get('/user-info', [ModuleAccessRequestController::class, 'userInfo'])->name('user-info');
    Route::get('/jeeva-access-requests/pdf', [JeevaAccessRequestController::class, 'exportPdf'])->name('jeeva-access-requests.pdf');
});
