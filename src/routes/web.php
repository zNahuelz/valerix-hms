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

    Route::group(['prefix' => '/nurse'], function () {
        Route::livewire('/', 'pages.nurse.nurse-index')
            ->middleware('require_permission:nurse.index')
            ->name('nurse.index');

        Route::livewire('/create', 'pages.nurse.nurse-form')
            ->middleware('require_permission:nurse.create')
            ->name('nurse.create');

        Route::livewire('/{nurseId}', 'pages.nurse.nurse-detail')
            ->middleware('require_permission:nurse.detail')
            ->name('nurse.detail');

        Route::livewire('/{nurseId}/edit', 'pages.nurse.nurse-form')
            ->middleware('require_permission:nurse.edit')
            ->name('nurse.edit');
    });

    Route::group(['prefix' => '/worker'], function () {
        Route::livewire('/', 'pages.worker.worker-index')
            ->middleware('require_permission:worker.index')
            ->name('worker.index');

        Route::livewire('/create', 'pages.worker.worker-form')
            ->middleware('require_permission:worker.create')
            ->name('worker.create');

        Route::livewire('/{workerId}', 'pages.worker.worker-detail')
            ->middleware('require_permission:worker.detail')
            ->name('worker.detail');

        Route::livewire('/{workerId}/edit', 'pages.worker.worker-form')
            ->middleware('require_permission:worker.edit')
            ->name('worker.edit');
    });

    Route::group(['prefix' => '/doctor'], function () {
        Route::livewire('/', 'pages.doctor.doctor-index')
            ->middleware('require_permission:doctor.index')
            ->name('doctor.index');

        Route::livewire('/create', 'pages.doctor.doctor-create-form')
            ->middleware('require_permission:doctor.create')
            ->name('doctor.create');

        Route::livewire('/{doctorId}', 'pages.doctor.doctor-detail')
            ->middleware('require_permission:doctor.detail')
            ->name('doctor.detail');

        Route::livewire('/{doctorId}/edit', 'pages.doctor.doctor-edit-form')
            ->middleware('require_permission:doctor.edit')
            ->name('doctor.edit');

        Route::livewire('/{doctorId}/availabilities/edit', 'pages.doctor.doctor-availabilities-form')
            ->middleware('require_permission:doctor.edit.availabilities')
            ->name('doctor.edit.availabilities');

    });

    Route::group(['prefix' => '/d/unav'], function () {
        Route::livewire('/create', 'pages.doctor.doctor-unavailabilities-form')
            ->middleware('require_permission:doctor.create.unavailabilities')
            ->name('doctor.create.unavailabilities');

        Route::livewire('/{unavId}/edit', 'pages.doctor.doctor-unavailabilities-form')
            ->middleware('require_permission:doctor.edit.unavailabilities')
            ->name('doctor.edit.unavailabilities');

        Route::livewire('/{doctorId}', 'pages.doctor.doctor-unavailabilities-detail')
            ->middleware('require_permission:doctor.detail.unavailabilities')
            ->name('doctor.detail.unavailabilities');
    });

    Route::group(['prefix' => '/system/holiday'], function () {
        Route::livewire('/', 'pages.system.holiday.holiday-index')
            ->middleware('require_permission:holiday.index')
            ->name('holiday.index');

        Route::livewire('/create', 'pages.system.holiday.holiday-form')
            ->middleware('require_permission:holiday.create')
            ->name('holiday.create');

        Route::livewire('/{holidayId}/edit', 'pages.system.holiday.holiday-form')
            ->middleware('require_permission:holiday.edit')
            ->name('holiday.edit');
    });

    Route::group(['prefix' => '/system/payment-type'], function () {
        Route::livewire('/', 'pages.payment-type.payment-type-index')
            ->middleware('require_permission:paymentType.index')
            ->name('paymentType.index');

        Route::livewire('/create', 'pages.payment-type.payment-type-form')
            ->middleware('require_permission:paymentType.create')
            ->name('paymentType.create');

        Route::livewire('/{paymentTypeId}/edit', 'pages.payment-type.payment-type-form')
            ->middleware('require_permission:paymentType.edit')
            ->name('paymentType.edit');
    });

    Route::group(['prefix' => '/system/voucher-type'], function () {
        Route::livewire('/', 'pages.voucher-type.voucher-type-index')
            ->middleware('require_permission:voucherType.index')
            ->name('voucherType.index');

        Route::livewire('/{voucherTypeId}', 'pages.voucher-type.voucher-type-detail')
            ->middleware('require_permission:voucherType.detail')
            ->name('voucherType.detail');

        Route::livewire('/create', 'pages.voucher-type.voucher-type-form')
            ->middleware('require_permission:voucherType.create')
            ->name('voucherType.create');

        Route::livewire('/{voucherTypeId}/edit', 'pages.voucher-type.voucher-type-form')
            ->middleware('require_permission:voucherType.edit')
            ->name('voucherType.edit');
    });

    Route::group(['prefix' => '/system/voucher-serie'], function () {
        Route::livewire('/', 'pages.voucher-serie.voucher-serie-index')
            ->middleware('require_permission:voucherSerie.index')
            ->name('voucherSerie.index');

        Route::livewire('/create', 'pages.voucher-serie.voucher-serie-form')
            ->middleware('require_permission:voucherSerie.create')
            ->name('voucherSerie.create');

        Route::livewire('/{voucherSerieId}/edit', 'pages.voucher-serie.voucher-serie-form')
            ->middleware('require_permission:voucherSerie.edit')
            ->name('voucherSerie.edit');
    });

    Route::group(['prefix' => '/treatment'], function () {
        Route::livewire('/', 'pages.treatment.treatment-index')
            ->middleware('require_permission:treatment.index')
            ->name('treatment.index');

        Route::livewire('/create', 'pages.treatment.treatment-form')
            ->middleware('require_permission:treatment.create')
            ->name('treatment.create');

        Route::livewire('/{treatmentId}', 'pages.treatment.treatment-detail')
            ->middleware('require_permission:treatment.detail')
            ->name('treatment.detail');

        Route::livewire('/{treatmentId}/edit', 'pages.treatment.treatment-form')
            ->middleware('require_permission:treatment.edit')
            ->name('treatment.edit');
    });

    Route::group(['prefix' => '/buy-order'], function () {
        Route::livewire('/', 'pages.buy-order.buy-order-index')
            ->middleware('require_permission:buyOrder.index')
            ->name('buyOrder.index');

        Route::livewire('/create', 'pages.buy-order.buy-order-form')
            ->middleware('require_permission:buyOrder.create')
            ->name('buyOrder.create');

        Route::livewire('/{buyOrderId}', 'pages.buy-order.buy-order-detail')
            ->middleware('require_permission:buyOrder.detail')
            ->name('buyOrder.detail');

        Route::livewire('/{buyOrderId}/edit', 'pages.buy-order.buy-order-form')
            ->middleware('require_permission:buyOrder.edit')
            ->name('buyOrder.edit');
    });

    Route::group(['prefix' => '/clinic-medicine'], function () {
        Route::livewire('/', 'pages.clinic-medicine.clinic-medicine-index')
            ->middleware('require_permission:clinicMedicine.index')
            ->name('clinicMedicine.index');

        Route::livewire('/create', 'pages.clinic-medicine.clinic-medicine-form')
            ->middleware('require_permission:clinicMedicine.create')
            ->name('clinicMedicine.create');

        Route::livewire('/{clinicMedicineId}', 'pages.clinic-medicine.clinic-medicine-detail')
            ->middleware('require_permission:clinicMedicine.detail')
            ->name('clinicMedicine.detail');

        Route::livewire('/{clinicMedicineId}/edit', 'pages.clinic-medicine.clinic-medicine-form')
            ->middleware('require_permission:clinicMedicine.edit')
            ->name('clinicMedicine.edit');
    });

    Route::group(['prefix' => '/setting'], function () {
        Route::livewire('/', 'pages.setting.setting-index')
            ->middleware('require_permission:setting.index')
            ->name('setting.index');

        Route::livewire('/create', 'pages.setting.setting-form')
            ->middleware('require_permission:setting.create')
            ->name('setting.create');

        Route::livewire('/{settingId}', 'pages.setting.setting-detail')
            ->middleware('require_permission:setting.detail')
            ->name('setting.detail');

        Route::livewire('/{settingId}/edit', 'pages.setting.setting-form')
            ->middleware('require_permission:setting.edit')
            ->name('setting.edit');
    });

})->middleware('auth');

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect('/');
})->name('logout');
