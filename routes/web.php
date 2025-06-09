<?php

use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\RazorController;
use App\Http\Controllers\Backend\SubscriptionController;
use App\Http\Controllers\Frontend\SiteController;
use App\Http\Controllers\PaytmController;
use App\Http\Middleware\CheckSubscription;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login'])->name('backend.login');
Route::get('/register', [AuthController::class, 'register'])->name('backend.register');
Route::post('/registerOrg', [AuthController::class, 'registerOrg'])->name('backend.registerOrg');
Route::post('/loginRequest', [AuthController::class, 'loginRequest'])->name('backend.loginRequest');
Route::get('/initiate', [PaytmController::class, 'initiate'])->name('initiate.payment');
Route::post('/payment', [PaytmController::class, 'pay'])->name('make.payment');
Route::post('/payment/status', [PaytmController::class, 'paymentCallback'])->name('status');
Route::get('/', [SiteController::class, 'index'])->name('frontend.index');

Route::prefix('admin')->middleware(['auth:web', CheckSubscription::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/organizations', [DashboardController::class, 'listOrganizations'])->name('dashboard.listorganizations');
    Route::get('/products', [ProductController::class, 'listProducts'])->name('listProducts');
    Route::post('/dashboard/organizations/toggleStatus/{id}/{status}', [DashboardController::class, 'toggleStatus'])->name('organizations.toggleStatus');

});

Route::prefix('admin')->middleware(['auth:web'])->group(function () {
    Route::get('/subscription/purchase', [SubscriptionController::class, 'showPurchasePage'])->name('subscription.purchase');
    Route::get('/subscription/purchase/{id}', [SubscriptionController::class, 'purchase'])->name('subscription.purchase.id');
    Route::get('/subscription/payment/{id}', [SubscriptionController::class, 'payment'])->name('subscription.payment');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
Route::middleware(['auth:web'])->group(function () {
    Route::get('/initiate', [PaytmController::class, 'initiate'])->name('initiate.payment');
    Route::post('/payment', [PaytmController::class, 'pay'])->name('make.payment');
    Route::post('/payment/status', [PaytmController::class, 'paymentCallback'])->name('status');
});
Route::middleware(['auth:web'])->group(function () {
    Route::get('/razor_callback', [RazorController::class, 'razor_callback'])->name('razor_callback');
    Route::post('/create-order', [RazorController::class, 'createOrder'])->name('create.order');
});