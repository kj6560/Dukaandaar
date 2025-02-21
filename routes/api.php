<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OrgController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Auth
Route::get('/login', [AuthController::class, 'login']);
Route::get('/registerOrg', [OrgController::class, 'register']);
Route::get('/registerUser', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout']);
