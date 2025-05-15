<?php

use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\PaytmController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login'])->name('backend.login');
Route::get('/initiate',[PaytmController::class,'initiate'])->name('initiate.payment');
Route::post('/payment',[PaytmController::class,'pay'])->name('make.payment');
Route::post('/payment/status', [PaytmController::class,'paymentCallback'])->name('status');