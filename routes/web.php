<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ShopListController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/shops', [ShopListController::class, 'index'])->name('shops');
});

// Print Routes
Route::get('scan', [PrintController::class, 'scan'])->name('scan');
Route::get('print/{shopUuid?}', [PrintController::class, 'index'])->name('print');
Route::post('print/upload', [PrintController::class, 'upload'])->name('print.upload');
Route::post('print/pay', [PrintController::class, 'processPayment'])->name('print.pay');

require __DIR__ . '/settings.php';