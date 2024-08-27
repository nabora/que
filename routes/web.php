<?php

use App\Http\Controllers\OfficeController;
use Illuminate\Support\Facades\Route;


Route::get('/', [OfficeController::class, 'user'])->name('user');
Route::post('/', [OfficeController::class, 'store'])->name('store');
Route::post('/printTransactions', [OfficeController::class, 'print'])->name('print');
Route::get('/printTransactions', [OfficeController::class, 'print'])->name('print');


