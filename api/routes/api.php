<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Middleware\BaseMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->middleware(BaseMiddleware::class)->group(function () {
    Route::group([
        'prefix' => '/auth',
    ], function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth');
    });

    Route::group(['prefix' => '/supplier'], function () {
        Route::post('/', [SupplierController::class, 'store'])->middleware('permission:supplier:store,sys:admin');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->middleware('permission:supplier:show,sys:admin');
        Route::get('/', [SupplierController::class, 'index'])->middleware('permission:supplier:index,sys:admin');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->middleware('permission:supplier:update,sys:admin');
        Route::patch('/{supplier}/restore', [SupplierController::class, 'restore'])->middleware('permission:supplier:restore,sys:admin');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->middleware('permission:supplier:destroy,sys:admin');
    });
});
