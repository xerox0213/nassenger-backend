<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/user', [AuthController::class, 'user'])
    ->name('auth.user')
    ->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register'])
    ->name('auth.register');