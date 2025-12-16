<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicOpenDoorController;


Route::get('/', function () {
    return view('welcome');
});

// Rutes públiques Portes Obertes
Route::prefix('portes-obertes')->name('open-doors.')->group(function () {
    Route::get('/', [PublicOpenDoorController::class, 'showForm'])->name('form');
    Route::post('/', [PublicOpenDoorController::class, 'submitForm'])->name('submit');
    Route::get('/success', [PublicOpenDoorController::class, 'success'])->name('success');
    Route::get('/confirmar/{token}', [PublicOpenDoorController::class, 'confirm'])->name('confirm');
    Route::get('/cancelar/{token}', [PublicOpenDoorController::class, 'cancel'])->name('cancel');
});

// API per Moodle/JS (sense CSRF per facilitar integració)
Route::prefix('api/open-doors')->group(function () {
    Route::get('/sessions', [PublicOpenDoorController::class, 'apiGetSessions']);
    Route::post('/register', [PublicOpenDoorController::class, 'apiSubmit']);
});
