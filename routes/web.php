<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaintenanceLogController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SiteCredentialController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('sites', SiteController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/sites/{site}/maintenance-logs', [MaintenanceLogController::class, 'store'])->name('sites.maintenance-logs.store');
    Route::post('/credentials/{credential}/reveal', [SiteCredentialController::class, 'reveal'])
        ->middleware(['password.confirm', 'throttle:6,1'])
        ->name('credentials.reveal');
    Route::post('/credentials/{credential}/copy', [SiteCredentialController::class, 'copy'])
        ->middleware(['password.confirm', 'throttle:12,1'])
        ->name('credentials.copy');
});
