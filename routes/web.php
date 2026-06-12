<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DepositController;
use Illuminate\Support\Facades\Route;

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// All protected routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Rooms
    Route::resource('rooms', RoomController::class);

    // Persons — explicit routes BEFORE the resource to avoid wildcard capture
    Route::get('persons/import/template', [PersonController::class, 'downloadTemplate'])->name('persons.import.template');
    Route::post('persons/import', [PersonController::class, 'import'])->name('persons.import');
    Route::get('persons/export', [PersonController::class, 'export'])->name('persons.export');
    Route::post('persons/{person}/transfer', [PersonController::class, 'transfer'])->name('persons.transfer');
    Route::post('persons/{id}/restore', [PersonController::class, 'restore'])->name('persons.restore');
    Route::resource('persons', PersonController::class);

    // Fees
    Route::get('fees/export', [FeeController::class, 'export'])->name('fees.export');
    Route::resource('fees', FeeController::class)->except(['show']);
    Route::post('fees/generate-monthly', [FeeController::class, 'generateMonthly'])->name('fees.generate');
    Route::post('fees/store-for-room', [FeeController::class, 'storeForRoom'])->name('fees.store_for_room');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('quarterly', [ReportController::class, 'quarterly'])->name('quarterly');
        Route::get('by-room', [ReportController::class, 'byRoom'])->name('by_room');
        Route::get('by-person', [ReportController::class, 'byPerson'])->name('by_person');
        Route::get('deposit', [DepositController::class, 'index'])->name('deposit');
        Route::get('deposit/export', [DepositController::class, 'export'])->name('deposit.export');
    });
});
