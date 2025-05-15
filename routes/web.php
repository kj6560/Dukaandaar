<?php

use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Frontend\SiteController;
use App\Http\Controllers\PaytmController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login'])->name('backend.login');
Route::post('/loginRequest', [AuthController::class, 'loginRequest'])->name('backend.loginRequest');
Route::get('/initiate',[PaytmController::class,'initiate'])->name('initiate.payment');
Route::post('/payment',[PaytmController::class,'pay'])->name('make.payment');
Route::post('/payment/status', [PaytmController::class,'paymentCallback'])->name('status');
Route::get('/', [SiteController::class, 'index'])->name('frontend.index');

Route::prefix('admin')->middleware(['auth:web'])->group(function () {
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
});