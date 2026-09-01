<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\School\SchoolProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;

// ── Public auth routes ─────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// ── Authenticated routes ───────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.alt');

    // School Profile & Configuration
    Route::prefix('school')->name('school.')->group(function () {
        Route::get('/profile', [SchoolProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [SchoolProfileController::class, 'update'])->name('profile.update');
        Route::get('/academic-years', [SchoolProfileController::class, 'academicYears'])->name('academic-years');
        Route::post('/academic-years', [SchoolProfileController::class, 'storeAcademicYear'])->name('academic-years.store');
        Route::put('/academic-years/{academicYear}', [SchoolProfileController::class, 'updateAcademicYear'])->name('academic-years.update');
        Route::post('/academic-years/{academicYear}/set-current', [SchoolProfileController::class, 'setCurrentYear'])->name('academic-years.set-current');
        Route::get('/terms', [SchoolProfileController::class, 'terms'])->name('terms');
        Route::post('/terms', [SchoolProfileController::class, 'storeTerm'])->name('terms.store');
        Route::put('/terms/{term}', [SchoolProfileController::class, 'updateTerm'])->name('terms.update');
        Route::get('/classes', [SchoolProfileController::class, 'classes'])->name('classes');
        Route::post('/classes', [SchoolProfileController::class, 'storeClass'])->name('classes.store');
        Route::put('/classes/{schoolClass}', [SchoolProfileController::class, 'updateClass'])->name('classes.update');
        Route::get('/subjects', [SchoolProfileController::class, 'subjects'])->name('subjects');
        Route::post('/subjects', [SchoolProfileController::class, 'storeSubject'])->name('subjects.store');
        Route::put('/subjects/{subject}', [SchoolProfileController::class, 'updateSubject'])->name('subjects.update');
        Route::get('/grading', [SchoolProfileController::class, 'grading'])->name('grading');
        Route::post('/grading', [SchoolProfileController::class, 'storeGrading'])->name('grading.store');
        Route::put('/grading/{scheme}', [SchoolProfileController::class, 'updateGrading'])->name('grading.update');
    });

    // Administration
    Route::prefix('admin')->name('admin.')->middleware('role:system_admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::post('roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.permissions.sync');
        Route::post('users/{user}/roles', [UserController::class, 'syncRoles'])->name('users.roles.sync');
    });
});
