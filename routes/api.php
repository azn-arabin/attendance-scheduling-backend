<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Student\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::middleware(['auth:api', 'role:student'])->prefix('student')->group(function () {
    Route::get('/upcoming-classes', [StudentController::class, 'upcomingClasses']);
    Route::post('/mark-attendance', [StudentController::class, 'markAttendance']);
});
