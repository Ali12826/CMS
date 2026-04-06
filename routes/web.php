<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;

// ==================== 1. PUBLIC ROUTES ====================

Route::view('/', 'landing')->name('landing');

// Authentication
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.post');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register')->name('register.post');
});


// ==================== 2. PROTECTED ROUTES (Auth Required) ====================

Route::middleware(['auth'])->group(function () {

    // --- DASHBOARD / GENERAL ---
    // FIX: Changed to match both GET and POST to prevent "MethodNotAllowed" if clicked via link
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

    // --- PROFILE MANAGEMENT ---
    Route::controller(ProfileController::class)->prefix('my-profile')->name('profile.')->group(function () {
        Route::get('/', 'showProfile')->name('show');
        Route::post('/', 'updateProfile')->name('update');
    });

    // --- SECURITY (First-Time Password) ---
    Route::controller(AuthController::class)->prefix('change-password-employee')->name('change.password.employee')->group(function () {
        Route::get('/', 'showChangePasswordEmployee')->name('');
        Route::post('/', 'changePasswordEmployee')->name('.post');
    });

    // --- TASK SYSTEM ---
    Route::prefix('tasks')->name('tasks.')->controller(TaskController::class)->group(function () {
        Route::get('/search/ajax', 'searchAjax')->name('search.ajax');
        Route::get('/filter/options', 'getFilterOptions')->name('filter.options');
    });

    Route::resource('tasks', TaskController::class);

    // --- REPORTS ---
    // FIX: Changed URL from '/admin/reports' to '/admin/employee-report' to match your error log
    Route::match(['get', 'post'], '/admin/employee-report', [ReportController::class, 'index'])->name('admin.reports.index');


    // ==================== 3. ADMIN PANEL ====================

    Route::middleware(['admin'])->prefix('admin')->name('admin.')->controller(AdminController::class)->group(function () {

        // --- 1. MANAGE ADMINS ---
        Route::prefix('admins')->group(function () {
            Route::get('/', 'manageAdmin')->name('manage');
            Route::post('/', 'storeAdmin')->name('store.admin');
            Route::get('/{id}/edit', 'editAdmin')->name('edit');
            Route::put('/{id}', 'updateAdmin')->name('update');

            // Admin Self-Password Update
            Route::get('/{id}/password', 'changePassword')->name('change_password');
            Route::post('/{id}/password', 'updatePassword')->name('update_password');
        });

        // --- 2. MANAGE USERS / EMPLOYEES ---
        Route::prefix('users')->group(function () {
            Route::get('/', 'manageUsers')->name('manage.users');
            Route::post('/', 'storeUser')->name('store.user');
            Route::get('/{id}/edit', 'editUser')->name('edit.user');
            Route::put('/{id}', 'updateUser')->name('update.user');
            Route::get('/{id}/delete', 'deleteUser')->name('delete.user');

            // Note: This route is for standard updates, but your modal uses the specific one below
            Route::post('/{id}/password-update', 'updateUserPassword')->name('update.user.pwd');
        });

        // --- 3. SPECIAL PASSWORD RESET FIX ---
        Route::post('/update-user-pwd/{id}', 'updateUserPassword')->name('update.user.pwd.direct');

        // Also keeping this as a backup alias
        Route::post('/reset-any-password/{id}', 'resetAnyPassword')->name('reset.any.password');

    });
});
