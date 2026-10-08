<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('admin.path'))->name('admin.')->group(function () {

    Route::redirect('/', '/'.config('admin.path').'/dashboard');

    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login'])->name('login.store');
    });

    Route::middleware(['auth', 'super_admin'])->group(function () {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        // Phase 4+ routes (users, salons, billing, payment-requests, profile, audit-logs) go here.
    });
});