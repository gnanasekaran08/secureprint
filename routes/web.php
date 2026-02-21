<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Print Routes
Route::get('scan', [PrintController::class, 'scan'])->name('scan');
Route::get('print/{shopUuid?}', [PrintController::class, 'index'])->name('print');
Route::post('print/upload', [PrintController::class, 'upload'])->name('print.upload');
Route::post('print/pay', [PrintController::class, 'processPayment'])->name('print.pay');

require __DIR__ . '/settings.php';
