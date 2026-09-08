<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MaintenanceLogController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SiteCredentialController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('sites', SiteController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/sites/{site}/check', [SiteController::class, 'check'])->name('sites.check');
    Route::get('/maintenance', [MaintenanceLogController::class, 'index'])->name('maintenance.index');
    Route::post('/sites/{site}/maintenance-logs', [MaintenanceLogController::class, 'store'])->name('sites.maintenance-logs.store');
    Route::get('/maintenance-logs/{maintenanceLog}/edit', [MaintenanceLogController::class, 'edit'])->name('maintenance-logs.edit');
    Route::put('/maintenance-logs/{maintenanceLog}', [MaintenanceLogController::class, 'update'])->name('maintenance-logs.update');
    Route::delete('/maintenance-logs/{maintenanceLog}', [MaintenanceLogController::class, 'destroy'])->name('maintenance-logs.destroy');
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/credentials/{credential}/reveal', [SiteCredentialController::class, 'reveal'])
        ->middleware(['password.confirm', 'throttle:6,1'])
        ->name('credentials.reveal');
    Route::post('/credentials/{credential}/copy', [SiteCredentialController::class, 'copy'])
        ->middleware(['password.confirm', 'throttle:12,1'])
        ->name('credentials.copy');
});
