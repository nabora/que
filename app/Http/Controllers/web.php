<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataCleanupController;
use App\Http\Controllers\QueueDisplayController;

Route::get('/', [OfficeController::class, 'user'])->name('user');
Route::post('/', [OfficeController::class, 'storeServices'])->name('storeServices');

Route::get('/reset-expired-data', [DataCleanupController::class, 'resetExpiredData'])
    ->name('reset.expired.data')
    ->middleware('auth');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::get('queue-display', [App\Http\Controllers\QueueDisplayController::class, 'index'])->name('queue-display');
Route::get('/fetch-queues', [QueueDisplayController::class, 'fetchQueues']);

Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard'); 
Route::post('/nextQueue', [DashboardController::class, 'nextQueue'])->name('nextQueue');
Route::post('/callQueue', [DashboardController::class, 'callQueue'])->name('callQueue');
Route::post('/repeatQueue', [DashboardController::class, 'repeatQueue'])->name('repeatQueue');
Route::post('/removeQueue', [DashboardController::class, 'removeQueue'])->name('removeQueue');


Route::post('/login', 'Auth\LoginController@logout')->name('logout');

Route::get('/super-dashboard', [OfficeController::class, 'dashboard'])->name('super.dashboard');
