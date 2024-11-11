<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataCleanupController;
use App\Http\Controllers\QueueDisplayController;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', [OfficeController::class, 'user'])->name('user');
Route::post('/', [OfficeController::class, 'store'])->name('store');
Route::post('/printTransactions', [OfficeController::class, 'print'])->name('print');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/reset-expired-data', [DataCleanupController::class, 'resetExpiredData'])
    ->name('reset.expired.data')
    ->middleware('auth');

Route::get('queue-display', [App\Http\Controllers\QueueDisplayController::class, 'index'])->name('queue-display');
Route::get('/fetch-queues', [QueueDisplayController::class, 'fetchQueues']);
Route::get('/pending-queue-numbers', [QueueDisplayController::class, 'getPendingQueueNumbers'])->name('pendingQueueNumbers');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/next-queue', [DashboardController::class, 'nextQueue'])->name('next.queue');
Route::post('/call-queue', [DashboardController::class, 'callQueue'])->name('call.queue');
Route::post('/remove-queue', [DashboardController::class, 'removeQueue'])->name('remove.queue');
Route::post('/login', 'Auth\LoginController@logout')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
    Route::post('/nextQueue', [DashboardController::class, 'nextQueue'])->name('nextQueue');
    Route::post('/callQueue', [DashboardController::class, 'callQueue'])->name('callQueue');
    Route::post('/repeatQueue', [DashboardController::class, 'repeatQueue'])->name('repeatQueue');
    Route::post('/removeQueue', [DashboardController::class, 'removeQueue'])->name('removeQueue');
    Route::post('/mark-absent', [DashboardController::class, 'markAbsent'])->name('markAbsent');
});

Route::post('/next-queue', [DashboardController::class, 'nextQueue'])->name('next.queue');

Route::post('/absent-queue', [DashboardController::class, 'absentQueue'])->name('absent.queue');

Route::post('/remove-absent-queue', [DashboardController::class, 'removeAbsentQueue'])->name('remove.absent.queue');

Route::get('/super-dashboard', [OfficeController::class, 'dashboard'])->name('super.dashboard');

Route::get('/super-dashboard', [OfficeController::class, 'dashboard'])->name('superDashboard');

Route::get('/office/{id}/details', [OfficeController::class, 'officeDetails'])->name('office.details');

require __DIR__ . '/auth.php';
