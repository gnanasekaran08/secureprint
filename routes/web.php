<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PrintJobListController;
use App\Http\Controllers\ShopListController;
use Illuminate\Support\Facades\Artisan;
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
Route::post('delete-print-job', [PrintController::class, 'deletePrintJobByDocNo'])->name('print.delete-job-by-docno');

require __DIR__ . '/settings.php';

Route::get('cls', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    $appVersion = app()->version();
    $phpVersion = phpversion();
    echo "App version: " . $appVersion;
    echo "<br>";
    echo "PHP version: " . $phpVersion;
    echo "<br>";
    return "Cache is cleared";
});

Route::get('symlink', function () {
    Artisan::call('storage:link');
    return "Sym link created";
});

Route::get('migrate-table', function () {
    Artisan::call('migrate', ['--force' => true]);
    return "Tables migrated";
});
