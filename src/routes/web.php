<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages.auth.login')->middleware('guest')->name('login');

Route::group(['prefix' => '/dashboard'], function () {
    Route::livewire('/', 'pages.shared.dashboard')->name('dashboard');
    Route::group(['prefix' => '/supplier'], function () {
        Route::livewire('/', 'pages.supplier.supplier-index')->name('supplier.index');
        Route::livewire('/create', 'pages.supplier.supplier-form')->name('supplier.create');
        Route::livewire('/{supplier}/edit', 'pages.supplier.supplier-form')->name('supplier.edit');
    });
})->middleware('auth');

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect('/');
})->name('logout');
