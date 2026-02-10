<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\BaseMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    Route::group([
        'prefix' => '/auth'
    ], function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:api');
    });

    Route::get('/test', [AuthController::class, 'test'])->middleware('auth:api', 'permission:sys:admin');
})->middleware(BaseMiddleware::class);
