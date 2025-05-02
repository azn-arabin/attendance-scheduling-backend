<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\BatchController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Instructor\ClassController;
use App\Http\Controllers\Api\Instructor\DashboardController;
use App\Http\Controllers\Api\Instructor\InstructorController;
use App\Http\Controllers\Api\Student\StudentController;
use Illuminate\Support\Facades\Route;

// Public APIs
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});
Route::get('/batches', [BatchController::class, 'index']);
Route::get('/batches/{id}', [BatchController::class, 'show']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::middleware(['auth:api', 'role:student'])->prefix('student')->group(function () {
    Route::get('/upcoming-classes', [StudentController::class, 'upcomingClasses']);
    Route::post('/mark-attendance', [StudentController::class, 'markAttendance']);
});

Route::middleware(['auth:api', 'role:instructor'])->prefix('instructor')->group(function () {
    // stats
    Route::get('dashboard-stats', [DashboardController::class, 'stats']);
    Route::get('class-distribution', [DashboardController::class, 'classDistribution']);
    Route::get('monthly-classes', [DashboardController::class, 'monthlyClasses']);

    Route::get('batches', [InstructorController::class, 'myBatches']);

    Route::resource('classes', ClassController::class);

    Route::get('stats/batch/{batch_id}', [InstructorController::class, 'batchStats']);
    Route::get('stats/top-student/{batch_id}', [InstructorController::class, 'topStudent']);
    Route::get('stats/trend/{batch_id}', [InstructorController::class, 'attendanceTrend']);
});

Route::middleware(['auth:api', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('batches', [AdminController::class, 'listBatches']);
    Route::get('batches/{batch_id}/students', [AdminController::class, 'listBatchStudents']);
    Route::get('batches/{batch_id}/instructors', [AdminController::class, 'listBatchInstructors']);
    Route::get('batches/{batch}/attendance/export', [AdminController::class, 'exportBatchAttendance']);


    Route::post('/batches', [BatchController::class, 'store']);
    Route::put('/batches/{id}', [BatchController::class, 'update']);
    Route::delete('/batches/{id}', [BatchController::class, 'destroy']);

    // Total attendance stats for a batch
    // Most present student
    // Attendance trend for past 30 days
    Route::get('/attendance-stats/{batch}', [AdminController::class, 'getBatchStats']);
});


