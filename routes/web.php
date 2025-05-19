<?php

use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\SubscriptionController;
use App\Http\Controllers\Frontend\SiteController;
use App\Http\Controllers\PaytmController;
use App\Http\Middleware\CheckSubscription;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login'])->name('backend.login');
Route::post('/loginRequest', [AuthController::class, 'loginRequest'])->name('backend.loginRequest');
Route::get('/initiate', [PaytmController::class, 'initiate'])->name('initiate.payment');
Route::post('/payment', [PaytmController::class, 'pay'])->name('make.payment');
Route::post('/payment/status', [PaytmController::class, 'paymentCallback'])->name('status');
Route::get('/', [SiteController::class, 'index'])->name('frontend.index');

Route::prefix('admin')->middleware(['auth:web', CheckSubscription::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});

Route::prefix('admin')->middleware(['auth:web'])->group(function () {
    Route::get('/subscription/purchase', [SubscriptionController::class, 'showPurchasePage'])->name('subscription.purchase');
    Route::get('/subscription/purchase/{id}', [SubscriptionController::class, 'purchase'])->name('subscription.purchase.id');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
Route::middleware(['auth:web'])->group(function () {
    Route::get('/initiate', [PaytmController::class, 'initiate'])->name('initiate.payment');
    Route::post('/payment', [PaytmController::class, 'pay'])->name('make.payment');
    Route::post('/payment/status', [PaytmController::class, 'paymentCallback'])->name('status');
});
