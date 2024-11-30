<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ConversationMessageController;
use Illuminate\Support\Facades\Route;

Route::get('/user', [AuthController::class, 'user'])
    ->name('auth.user')
    ->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register'])
    ->name('auth.register');

Route::post('/login', [AuthController::class, 'login'])
    ->name('auth.login');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('auth.logout');

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::apiResource('conversations', ConversationController::class)
        ->only('index', 'store', 'destroy');

    Route::apiResource('members', MemberController::class)
        ->only('index');

    Route::apiResource('conversations.messages', ConversationMessageController::class)
        ->only('index', 'store', 'update', 'destroy');

});
