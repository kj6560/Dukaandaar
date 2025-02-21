<?php

use App\Http\Controllers\Backend\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login'])->name('backend.login');
