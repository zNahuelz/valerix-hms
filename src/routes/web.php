<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages.auth.login')->middleware('guest')->name('login');

Route::group(['prefix' => '/dashboard'], function () {
    Route::livewire('/', 'pages.shared.dashboard')->name('dashboard');

    Route::group(['prefix' => '/clinic'], function () {
        Route::livewire('/', 'pages.clinic.clinic-index')
            ->middleware('require_permission:clinic.index')
            ->name('clinic.index');

        Route::livewire('/create', 'pages.clinic.clinic-form')
            ->middleware('require_permission:clinic.create')
            ->name('clinic.create');

        Route::livewire('/{clinicId}', 'pages.clinic.clinic-detail')
            ->middleware('require_permission:clinic.detail')
            ->name('clinic.detail');

        Route::livewire('/{clinicId}/edit', 'pages.clinic.clinic-form')
            ->middleware('require_permission:clinic.edit')
            ->name('clinic.edit');
    });

    Route::group(['prefix' => '/supplier'], function () {
        Route::livewire('/', 'pages.supplier.supplier-index')
            ->middleware('require_permission:supplier.index')
            ->name('supplier.index');

        Route::livewire('/create', 'pages.supplier.supplier-form')
            ->middleware('require_permission:supplier.create')
            ->name('supplier.create');

        Route::livewire('/{supplierId}', 'pages.supplier.supplier-detail')
            ->middleware('require_permission:supplier.detail')
            ->name('supplier.detail');

        Route::livewire('/{supplierId}/edit', 'pages.supplier.supplier-form')
            ->middleware('require_permission:supplier.edit')
            ->name('supplier.edit');
    });

    Route::group(['prefix' => '/patient'], function () {
        Route::livewire('/', 'pages.patient.patient-index')
            ->middleware('require_permission:patient.index')
            ->name('patient.index');

        Route::livewire('/create', 'pages.patient.patient-form')
            ->middleware('require_permission:patient.create')
            ->name('patient.create');

        Route::livewire('/{patientId}', 'pages.patient.patient-detail')
            ->middleware('require_permission:patient.detail')
            ->name('patient.detail');

        Route::livewire('/{patientId}/edit', 'pages.patient.patient-form')
            ->middleware('require_permission:patient.edit')
            ->name('patient.edit');
    });

    Route::group(['prefix' => '/presentation'], function () {
        Route::livewire('/', 'pages.presentation.presentation-index')
            ->middleware('require_permission:presentation.index')
            ->name('presentation.index');

        Route::livewire('/create', 'pages.presentation.presentation-form')
            ->middleware('require_permission:presentation.create')
            ->name('presentation.create');

        Route::livewire('/{presentationId}', 'pages.presentation.presentation-detail')
            ->middleware('require_permission:presentation.detail')
            ->name('presentation.detail');

        Route::livewire('/{presentationId}/edit', 'pages.presentation.presentation-form')
            ->middleware('require_permission:presentation.edit')
            ->name('presentation.edit');
    });

    Route::group(['prefix' => '/medicine'], function () {
        Route::livewire('/', 'pages.medicine.medicine-index')
            ->middleware('require_permission:medicine.index')
            ->name('medicine.index');

        Route::livewire('/create', 'pages.medicine.medicine-form')
            ->middleware('require_permission:medicine.create')
            ->name('medicine.create');

        Route::livewire('/{medicineId}', 'pages.medicine.medicine-detail')
            ->middleware('require_permission:medicine.detail')
            ->name('medicine.detail');

        Route::livewire('/{medicineId}/edit', 'pages.medicine.medicine-form')
            ->middleware('require_permission:medicine.edit')
            ->name('medicine.edit');
    });

})->middleware('auth');

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect('/');
})->name('logout');
