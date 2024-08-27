<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\DataCleanupController;
use App\Http\Controllers\QueueDisplayController;


Route::get('/', [OfficeController::class, 'user'])->name('user');
Route::post('/', [OfficeController::class, 'store'])->name('store');
Route::post('/printTransactions', [OfficeController::class, 'print'])->name('print');

Route::get('/reset-expired-data', [DataCleanupController::class, 'resetExpiredData'])
    ->name('reset.expired.data')
    ->middleware('auth');
    
    Route::get('queue-display', [App\Http\Controllers\QueueDisplayController::class, 'index'])->name('queue-display');
    Route::get('/fetch-queues', [QueueDisplayController::class, 'fetchQueues']);
    


