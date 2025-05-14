<?php

use App\Http\Controllers\Api\AppContact\ContactUsController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OrgController;
use App\Http\Controllers\Api\Barcode\BarcodeController;
use App\Http\Controllers\Api\Cutomers\CustomerController;
use App\Http\Controllers\Api\Home\HomeController;
use App\Http\Controllers\Api\Inventory\InventoryController;
use App\Http\Controllers\Api\Inventory\ProductController;
use App\Http\Controllers\Api\Orders\OrderController;
use App\Http\Controllers\Api\schemes\ProductSchemeController;
use App\Http\Controllers\Api\Users\UserController;
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
    Route::get('/fetchProductUoms', [ProductController::class, 'fetchProductUoms']);
    Route::get('/deleteProduct', [ProductController::class, 'deleteProduct']);

    //orders
    Route::get('/fetchOrders', [OrderController::class, 'fetchOrders']);
    Route::post('/updateOrder', [OrderController::class, 'updateOrder']);
    Route::get('/fetchCustomerOrders', [OrderController::class, 'fetchCustomerOrders']);

    //customers
    Route::get('/fetchCustomers', [CustomerController::class, 'fetchCustomers']);
    Route::post('/createCustomer', [CustomerController::class, 'createCustomer']);

    //barcode
    Route::get('/generateBarcode', [BarcodeController::class, 'generateBarcode']);

    //schemes
    Route::get('/allSchemes', [ProductSchemeController::class, 'index']);
    Route::post('/updateScheme', [ProductSchemeController::class, 'store']);
    Route::get('/fetchSchemes', [ProductSchemeController::class, 'show']);
    Route::post('/deleteScheme', [ProductSchemeController::class, 'destroy']);

    //app contacts
    Route::post('/createContactFromApp', [ContactUsController::class, 'contactFromApp']);
    Route::get('/fetchContactResponses', [ContactUsController::class, 'fetchContactResponses']);

    //users
    Route::post('/user/profile-picture', [UserController::class, 'updateProfilePicture']);
});
