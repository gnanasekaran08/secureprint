<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PrintJobListController;
use App\Http\Controllers\ShopListController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/shops', [ShopListController::class, 'index'])->name('shops');
    Route::get('/print-jobs', [PrintJobListController::class, 'index'])->name('print-jobs');
    Route::post('/print-jobs/{uuid}/delete-files', [PrintJobListController::class, 'removeFiles'])->name('print-jobs.remove-files');
});

// Print Routes
Route::get('scan', [PrintController::class, 'scan'])->name('scan');
Route::get('print/{shop_uuid?}', [PrintController::class, 'print'])->name('print');
Route::post('print/upload', [PrintController::class, 'upload'])->name('print.upload');
Route::post('print/pay', [PrintController::class, 'processPayment'])->name('print.pay');
Route::post('print/verify-otp', [PrintController::class, 'verifyOtp'])->name('print.verify-otp');

require __DIR__ . '/settings.php';
