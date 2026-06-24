<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaterLevelController;

Route::redirect('/', 'login');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', [WaterLevelController::class, 'index'])->name('monitoring');
    Route::get('/riwayat', [WaterLevelController::class, 'riwayat'])->name('riwayat');
    Route::fallback(function () {
        return redirect()->route('monitoring');
    });
});

Route::get('/api/water-level/latest', [WaterLevelController::class, 'apiLatest'])->name('api.water.latest');
Route::get('/api/water-level/history', [WaterLevelController::class, 'apiHistory'])->name('api.water.history');
