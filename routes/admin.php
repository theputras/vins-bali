<?php

use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Admin\CarCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\CarImageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->prefix('vbpanel')->name('admin-panel.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Cars CRUD
    Route::post('cars/reorder', [CarController::class, 'updateOrder'])->name('cars.reorder');
    Route::resource('cars', CarController::class)->except(['show']);
    Route::resource('car-categories', CarCategoryController::class)->only(['store', 'update', 'destroy']);
    Route::resource('services', ServiceController::class)->except(['create', 'show', 'edit']);
    Route::patch('cars/{car}/toggle-availability', [CarController::class, 'toggleAvailability'])
        ->name('cars.toggle-availability');

    // Car Images
    Route::patch('car-images/{carImage}/set-primary', [CarImageController::class, 'setPrimary'])
        ->name('car-images.set-primary');
    Route::patch('car-images/reorder', [CarImageController::class, 'reorder'])
        ->name('car-images.reorder');
    Route::delete('car-images/{carImage}', [CarImageController::class, 'destroy'])
        ->name('car-images.destroy');

    // Users CRUD
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('users/{user}/toggle-role', [UserController::class, 'toggleRole'])
        ->name('users.toggle-role');

    // Settings
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
});
