<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserController;

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register'])->name('register');

Route::get('student', [UserController::class, 'studentDashboard']);
Route::get('teacher', [UserController::class, 'index']);
Route::get('students', [UserController::class, 'index'])->name('students');
Route::post('createClass', [ClassController::class, 'store'])->name('createClass');
Route::post('createSession', [SessionController::class, 'store'])->name('createSession');
Route::post('markAttendance', [AttendanceController::class, 'store'])->name('markAttendance');




// Route::apiResource('users', UserController::class);

// Route::middleware(['ensureAuthorized'])->group(function () {
//     Route::apiResource('classes', ClassController::class);
//     Route::apiResource('attendance', AttendanceController::class);
//     Route::apiResource('sessions', SessionController::class);
// });
