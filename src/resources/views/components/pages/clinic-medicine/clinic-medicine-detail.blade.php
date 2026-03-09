<?php

use Livewire\Component;
use App\Models\ClinicMedicine;

new class extends Component {
    public ?ClinicMedicine $clinicMedicine = null;

    public function mount(?string $clinicMedicineId = null): void
    {
        if ($clinicMedicineId) {
            if (!is_numeric($clinicMedicineId)) {
                $this->redirectWithError($clinicMedicineId);
                return;
            }

            $clinicMedicine = ClinicMedicine::withTrashed()
                ->with([
                    'clinic',
                    'medicine.presentation',
                    'lastSoldBy',
                ])
                ->find((int)$clinicMedicineId);

            if (!$clinicMedicine) {
                $this->redirectWithError($clinicMedicineId);
                return;
            }

            $this->clinicMedicine = $clinicMedicine;
        }
    }

    protected function redirectWithError($clinicMedicineId): void
    {
        Session::flash('error', __('clinic-medicine.errors.not_found', ['id' => $clinicMedicineId]));
        $this->redirectRoute('clinicMedicine.index');
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('clinic-medicine.detail', ['id' => $this->clinicMedicine?->id])])
            ->title(__('views.clinic_medicine.detail'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-3xl space-y-2">
        @if ($this->clinicMedicine && $this->clinicMedicine->trashed())
            <x-shared.alert type="info">{{ __('clinic-medicine.is_deleted_alt') }}</x-shared.alert>
        @endif
        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:field>
                <flux:label>{{ trans_choice('clinic.clinic', 1) }}</flux:label>
                <flux:input.group>
                    <flux:input readonly value="{{ $clinicMedicine->clinic->name ?? __('common.null') }}" type="text"/>
                    @if ($clinicMedicine->clinic)
                        <flux:button type="button" variant="primary" color="cyan"
                                     icon="ellipsis-horizontal" wire:navigate
                                     href="{{ route('clinic.detail', ['clinicId' => $clinicMedicine->clinic->id]) }}"/>
                    @endif
                </flux:input.group>
            </flux:field>
            <flux:field>
                <flux:label>{{ trans_choice('medicine.medicine', 1) }}</flux:label>
                <flux:input.group>
                    <flux:input readonly value="{{ $clinicMedicine->medicine->name ?? __('common.null') }}"
                                type="text"/>
                    @if ($clinicMedicine->medicine)
                        <flux:button type="button" variant="primary" color="cyan"
                                     icon="ellipsis-horizontal" wire:navigate
                                     href="{{ route('medicine.detail', ['medicineId' => $clinicMedicine->medicine->id]) }}"/>
                    @endif
                </flux:input.group>
            </flux:field>
            <flux:field>
                <flux:label>{{ trans_choice('presentation.presentation', 1) }}</flux:label>
                <flux:input readonly
                            value="{{ $clinicMedicine->medicine?->presentation?->description ?? __('common.null') }}"
                            type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('clinic-medicine.salable') }}</flux:label>
                <flux:input readonly
                            value="{{ $clinicMedicine->salable ? __('common.yes') : __('common.no') }}"
                            type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.buy_price') }}</flux:label>
                <flux:input readonly
                            value="{{ __('common.main_money_alt') . ' ' . number_format($clinicMedicine->buy_price, 2) }}"
                            type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.sell_price') }}</flux:label>
                <flux:input readonly
                            value="{{ __('common.main_money_alt') . ' ' . number_format($clinicMedicine->sell_price, 2) }}"
                            type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.tax') }} (18%)</flux:label>
                <flux:input readonly
                            value="{{ __('common.main_money_alt') . ' ' . number_format($clinicMedicine->tax, 2) }}"
                            type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.profit') }}</flux:label>
                <flux:input readonly
                            value="{{ __('common.main_money_alt') . ' ' . number_format($clinicMedicine->profit, 2) }}"
                            type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.stock') }}</flux:label>
                <flux:input readonly
                            value="{{ number_format($clinicMedicine->stock, 0) }}"
                            type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('clinic-medicine.last_sold_by') }}</flux:label>
                @if ($clinicMedicine->lastSoldBy)
                    <flux:input.group>
                        <flux:input readonly value="{{ $clinicMedicine->lastSoldBy->name ?? __('common.null') }}"
                                    type="text"/>
                        <flux:button type="button" variant="primary" color="cyan"
                                     icon="ellipsis-horizontal" wire:navigate
                                     href="{{ route('supplier.detail', ['supplierId' => $clinicMedicine->lastSoldBy->id]) }}"/>
                    </flux:input.group>
                @else
                    <flux:input readonly value="{{ __('common.null') }}" type="text"/>
                @endif
            </flux:field>
            <flux:input readonly
                        value="{{ $clinicMedicine->created_at?->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.created_at') }}"
                        type="text"/>
            <flux:input readonly
                        value="{{ $clinicMedicine->updated_at?->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.updated_at_alt') }}"
                        type="text"/>
            @if ($clinicMedicine->trashed())
                <flux:input readonly
                            value="{{ $clinicMedicine->deleted_at?->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                            label="{{ __('common.deleted_at') }}"
                            type="text"/>
            @endif
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-end gap-2">
                    @canany(['sys.admin', 'clinicMedicine.update'])
                        <flux:button
                            type="button"
                            variant="primary"
                            class="w-full md:w-auto"
                            wire:navigate
                            href="{{ route('clinicMedicine.edit', ['clinicMedicineId' => $clinicMedicine->id]) }}"
                        >
                            {{ __('common.edit') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </div>
</div>
