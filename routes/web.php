<?php

use App\Http\Controllers\OfficeController;
use Illuminate\Support\Facades\Route;

//LOGIN
Route::get('/login', function () {return view('login');});

Route::get('/', [OfficeController::class, 'user'])->name('user');

Route::post('/', [OfficeController::class, 'storeServices'])->name('user');

