<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OrgController;
use App\Http\Controllers\Api\Home\HomeController;
use App\Http\Controllers\Api\Inventory\InventoryController;
use App\Http\Controllers\Api\Inventory\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Auth
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/registerOrg', [OrgController::class, 'register']);
Route::get('/registerUser', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:api')->group(function () {
    //Home
    Route::get('/fetchKpi', [HomeController::class, 'fetchKpi']);
    //Inventory
    Route::get('/fetchInventory', [InventoryController::class, 'list']);
    Route::post('/updateInventory', [InventoryController::class, 'updateInventory']);

    //Product
    Route::post('/addProduct', [ProductController::class, 'addProduct']);
    Route::get('/fetchProducts', [ProductController::class, 'fetchProducts']);
    Route::get('/deleteProduct', [ProductController::class, 'deleteProduct']);
});
