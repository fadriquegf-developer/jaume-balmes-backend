<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OpenDoorsDashboardController;
use App\Http\Controllers\Admin\OpenDoorRegistrationCrudController;
use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
     // Dashboard General (futur)
    Route::get('dashboard', [DashboardController::class, 'index'])->name('backpack.dashboard');
    // Dashboard Inscripcions (Portes Obertes)
    Route::get('open-doors/dashboard', [OpenDoorsDashboardController::class, 'index'])->name('open-doors.dashboard');
    // Export
    Route::get('open-door-registration/export', [OpenDoorRegistrationCrudController::class, 'export'])->name('open-door-registration.export');

    Route::crud('open-door-session', 'OpenDoorSessionCrudController');
    Route::crud('open-door-registration', 'OpenDoorRegistrationCrudController');
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
