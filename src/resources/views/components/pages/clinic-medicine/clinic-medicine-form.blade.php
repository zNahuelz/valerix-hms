<?php

use App\Livewire\Forms\ClinicMedicineForm;
use App\Models\Clinic;
use App\Models\ClinicMedicine;
use App\Models\Medicine;
use Livewire\Component;

new class extends Component {
    public ClinicMedicineForm $form;

    public array $clinics = [];

    public string $barcode = '';
    public ?string $barcodeError = null;
    public ?string $foundMedicineName = null;
    public bool $duplicateClinicWarning = false;
    public ?int $existingClinicMedicineId = null;

    public function mount(?string $clinicMedicineId = null): void
    {
        if (!Clinic::whereNull('deleted_at')->exists()) {
            Session::flash('error', __('clinic-medicine.errors.creation_disabled_empty_clinics'));
            $this->redirectRoute('clinicMedicine.index');
            return;
        }
        $this->clinics = Clinic::select(['id', 'name'])->whereNull('deleted_at')->orderBy('name')->get()->toArray();

        $this->form->clinic_id = $this->clinics[0]['id'];

        if ($clinicMedicineId) {
            if (!is_numeric($clinicMedicineId)) {
                $this->redirectWithError($clinicMedicineId);
                return;
            }

            $clinicMedicine = ClinicMedicine::withTrashed()->with('medicine')->find((int)$clinicMedicineId);

            if (!$clinicMedicine) {
                $this->redirectWithError($clinicMedicineId);
                return;
            }

            $this->form->clinicMedicine = $clinicMedicine;
            $this->form->clinic_id = $clinicMedicine->clinic_id;

            $this->form->medicine_id = $clinicMedicine->medicine_id;
            $this->barcode = $clinicMedicine->medicine->barcode ?? '';
            $this->foundMedicineName = $clinicMedicine->medicine->name ?? '';

            $this->form->buy_price = $clinicMedicine->buy_price;
            $this->form->sell_price = $clinicMedicine->sell_price;
            $this->form->tax = $clinicMedicine->tax;
            $this->form->profit = $clinicMedicine->profit;
            $this->form->stock = $clinicMedicine->stock;
            $this->form->salable = (bool)$clinicMedicine->salable;
        }
    }

    protected function redirectWithError($clinicMedicineId)
    {
        Session::flash('error', __('clinic-medicine.errors.not_found', ['id' => $clinicMedicineId]));
        $this->redirectRoute('clinicMedicine.index');
    }

    public function updatedFormSellPrice(): void
    {
        $this->recalculate();
    }

    public function updatedFormBuyPrice(): void
    {
        $this->recalculate();
    }

    public function updatedFormClinicId(): void
    {
        if ($this->form->medicine_id) {
            $this->checkDuplicate();
        }
    }

    private function recalculate(): void
    {
        $sell = (float)$this->form->sell_price;
        $buy = (float)$this->form->buy_price;

        if ($sell > 0) {
            $taxRate = 0.18;
            $base = $sell / (1 + $taxRate);
            $this->form->tax = round($sell - $base, 4);
            $this->form->profit = $buy > 0 ? round($base - $buy, 4) : 0;
        } else {
            $this->form->tax = 0;
            $this->form->profit = 0;
        }
    }

    public function searchByBarcode(): void
    {
        $this->barcodeError = null;

        if (blank($this->barcode)) {
            $this->barcodeError = __('validation.barcode.required');
            return;
        }

        $medicine = Medicine::where('barcode', trim($this->barcode))->first();

        if (!$medicine) {
            $this->barcodeError = __('validation.barcode.not_found');
            return;
        }

        $this->form->medicine_id = $medicine->id;
        $this->foundMedicineName = $medicine->name;

        $this->checkDuplicate();
    }

    public function clearMedicine(): void
    {
        $this->form->medicine_id = '';
        $this->foundMedicineName = null;
        $this->barcode = '';
        $this->barcodeError = null;
        $this->duplicateClinicWarning = false;
        $this->existingClinicMedicineId = null;
    }

    public function clearDuplicateWarning(): void
    {
        $this->redirectRoute('clinicMedicine.index');
    }

    private function checkDuplicate(): void
    {
        if (!$this->form->clinic_id || !$this->form->medicine_id) {
            return;
        }

        $existing = ClinicMedicine::withTrashed()
            ->where('clinic_id', $this->form->clinic_id)
            ->where('medicine_id', $this->form->medicine_id)
            ->first();

        if ($existing && $existing->id !== $this->form->clinicMedicine?->id) {
            $this->duplicateClinicWarning = true;
            $this->existingClinicMedicineId = $existing->id;
        }
    }

    public function goToEdit(): void
    {
        $this->redirect(
            route('clinicMedicine.edit', ['clinicMedicineId' => $this->existingClinicMedicineId]),
            navigate: true
        );
    }

    public function save()
    {
        $data = $this->validate();
        try {
            if ($this->form->clinicMedicine) {
                $this->form->clinicMedicine->update($this->form->sanitized());
                Session::flash('success', __('clinic-medicine.updated', ['id' => $this->form->clinicMedicine->id]));
            } else {
                $clinicMedicine = ClinicMedicine::create($this->form->sanitized());
                Session::flash('success', __('clinic-medicine.created', ['id' => $clinicMedicine->id, 'name' => $clinicMedicine->medicine->name, 'clinic' => $clinicMedicine->clinic->name]));
            }
        } catch (Exception) {
            Session::flash('error', $this->form->clinicMedicine ? __('clinic-medicine.errors.update_failed') : __('clinic-medicine.errors.creation_failed'));
            return redirect()->to(route('clinicMedicine.index'));
        }
        return redirect()->to(route('clinicMedicine.index'));
    }

    public function delete()
    {
        if ($this->form->clinicMedicine) {
            if ($this->form->clinicMedicine->trashed()) {
                $this->form->clinicMedicine->restore();
                Session::flash('success', __('clinic-medicine.restored', ['id' => $this->form->clinicMedicine->id]));
            } else {
                $this->form->clinicMedicine->delete();
                Session::flash('success', __('clinic-medicine.deleted', ['id' => $this->form->clinicMedicine->id]));
            }
        }
        return redirect()->to(route('clinicMedicine.index'));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->clinicMedicine ? 'clinic-medicine.edit' : 'clinic-medicine.create')])
            ->title(__($this->form->clinicMedicine ? 'views.clinic_medicine.edit' : 'views.clinic_medicine.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-4xl" wire:submit="save">
        <flux:fieldset wire:loading.attr="disabled" wire:target="save, delete, searchByBarcode" class="space-y-2">
            <flux:card class="space-y-4">
                <flux:heading>{{ trans_choice('clinic.clinic', 1) }}</flux:heading>
                <flux:field>
                    <flux:label badge="{{ __('common.required') }}">
                        {{ trans_choice('clinic.clinic', 1) }}
                    </flux:label>
                    <flux:select wire:model.live.blur="form.clinic_id">
                        @foreach ($clinics as $clinic)
                            <flux:select.option value="{{ $clinic['id'] }}">{{ $clinic['name'] }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.clinic_id"/>
                </flux:field>
            </flux:card>

            <flux:card class="space-y-4">
                <flux:heading>{{ trans_choice('medicine.medicine', 1) }}</flux:heading>
                @if (!$form->medicine_id)
                    <div class="flex flex-col sm:flex-row gap-2">
                        <flux:field class="flex-1">
                            <flux:label>{{ __('common.barcode') }}</flux:label>
                            <flux:input
                                wire:model="barcode"
                                wire:keydown.enter.prevent="searchByBarcode"
                                type="text"
                                autocomplete="off"
                            />
                        </flux:field>
                        <div class="flex items-end">
                            <flux:button
                                type="button"
                                variant="primary"
                                wire:click="searchByBarcode"
                                wire:loading.attr="disabled"
                                wire:target="searchByBarcode"
                            >
                                {{ __('common.search') }}
                            </flux:button>
                        </div>
                    </div>
                    @if ($barcodeError)
                        <flux:text class="text-red-600 dark:text-red-400 text-sm">{{ $barcodeError }}</flux:text>
                    @endif
                    <div
                        class="flex flex-col items-center justify-center py-8 rounded-lg border border-dashed border-zinc-200 dark:border-zinc-700 text-zinc-400 dark:text-zinc-500">
                        <flux:text class="text-sm">{{ __('clinic-medicine.scan_to_search') }}</flux:text>
                    </div>
                @else
                    <div
                        class="flex items-start justify-between gap-3 p-4 rounded-lg bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 shrink-0">
                                <flux:icon.beaker/>
                            </div>
                            <div>
                                <flux:text
                                    class="font-medium text-zinc-900 dark:text-zinc-100">{{ $foundMedicineName }}</flux:text>
                                <flux:text
                                    class="text-xs font-mono text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $barcode }}</flux:text>
                            </div>
                        </div>
                        @if (!$form->clinicMedicine)
                            <flux:button type="button" variant="ghost" size="sm" icon="x-mark"
                                         wire:click="clearMedicine" class="text-zinc-400 hover:text-zinc-600"/>
                        @endif
                    </div>
                    @if ($duplicateClinicWarning)
                        <div
                            class="rounded-lg border border-amber-300 dark:border-amber-700 bg-amber-50 dark:bg-amber-950/40 p-4">
                            <div class="flex-1">
                                <flux:text
                                    class="text-sm font-medium text-amber-800 dark:text-amber-300">{{ __('clinic-medicine.already_assigned') }}</flux:text>
                                <flux:text
                                    class="text-xs text-amber-700 dark:text-amber-400 mt-1">{{ __('clinic-medicine.already_assigned_description') }}</flux:text>
                                <div class="mt-3 flex gap-2">
                                    <flux:button type="button" size="sm" variant="primary" color="amber"
                                                 wire:click="goToEdit">
                                        {{ __('clinic-medicine.edit_existing') }}
                                    </flux:button>
                                    <flux:button type="button" size="sm" variant="ghost"
                                                 wire:click="clearDuplicateWarning">
                                        {{ __('common.cancel') }}
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    @endif
                    <flux:error name="form.medicine_id"/>
                @endif
            </flux:card>
            @if ($form->medicine_id && !$duplicateClinicWarning)
                {{-- ── Pricing ── --}}
                <flux:card class="space-y-4">
                    <flux:heading>{{ __('common.pricing') }}</flux:heading>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label badge="{{ __('common.required') }}">{{ __('common.buy_price') }}</flux:label>
                            <flux:input wire:model.live="form.buy_price" type="number" min="0.01" step="any"
                                        icon-trailing="currency-dollar"/>
                            <flux:error name="form.buy_price"/>
                        </flux:field>
                        <flux:field>
                            <flux:label badge="{{ __('common.required') }}">
                                {{ __('common.sell_price') }}
                                <span class="ml-1 text-xs font-normal text-zinc-400">({{ __('clinic-medicine.includes_tax') }})</span>
                            </flux:label>
                            <flux:input wire:model.live="form.sell_price" type="number" min="0.01" step="any"
                                        icon-trailing="currency-dollar"/>
                            <flux:error name="form.sell_price"/>
                        </flux:field>
                    </div>
                    @if ($form->buy_price > 0 && $form->sell_price > 0)
                        <div
                            class="rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 divide-y divide-zinc-200 dark:divide-zinc-700 overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-2.5 text-sm">
                                <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('common.tax') }}(18%)
                                </flux:text>
                                <span
                                    class="font-mono font-medium text-zinc-700 dark:text-zinc-300">{{ number_format($form->tax, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-2.5 text-sm">
                                <flux:text
                                    class="text-zinc-500 dark:text-zinc-400">{{ __('common.profit') }}</flux:text>
                                <span
                                    class="font-mono font-medium {{ $form->profit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                        {{ number_format($form->profit, 2) }}
                    </span>
                            </div>
                        </div>
                        @if ($form->profit < 0)
                            <flux:text
                                class="text-xs text-red-500 dark:text-red-400">{{ __('clinic-medicine.negative_profit_warning') }}</flux:text>
                        @endif
                    @endif
                </flux:card>

                {{-- ── Stock & Settings ── --}}
                <flux:card class="space-y-4">
                    <flux:heading>{{ __('common.stock_settings') }}</flux:heading>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label badge="{{ __('common.required') }}">{{ __('common.stock') }}</flux:label>
                            <flux:input wire:model.live="form.stock" type="number" min="0" step="1" placeholder="0"/>
                            <flux:error name="form.stock"/>
                        </flux:field>
                        <flux:field class="flex flex-col justify-end pb-1">
                            <div
                                class="flex items-center justify-between rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/60 px-4 py-3">
                                <div>
                                    <flux:text
                                        class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ __('clinic-medicine.salable') }}</flux:text>
                                    <flux:text
                                        class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ __('clinic-medicine.salable_description') }}</flux:text>
                                </div>
                                <flux:switch wire:model.live="form.salable"/>
                            </div>
                            <flux:error name="form.salable"/>
                        </flux:field>
                    </div>
                </flux:card>

                {{-- ── Actions ── --}}
                <div class="flex flex-col-reverse sm:flex-row sm:justify-between gap-2 pt-1">
                    @if ($form->clinicMedicine)
                        @canany(['sys.admin', 'clinicMedicine.delete', 'clinicMedicine.restore'])
                            <flux:button type="button" variant="primary"
                                         color="{{ $form->clinicMedicine->trashed() ? 'amber' : 'red' }}"
                                         wire:click="delete" wire:loading.attr="disabled" wire:target="delete, save"
                                         class="w-full sm:w-auto"
                                         icon="{{ $form->clinicMedicine->trashed() ? 'arrow-path' : 'trash' }}">
                                {{ $form->clinicMedicine->trashed() ? __('common.restore') : __('common.delete') }}
                            </flux:button>
                        @endcanany
                    @endif
                    @canany(['sys.admin', 'clinicMedicine.create', 'clinicMedicine.update'])
                        <flux:button type="submit" variant="primary"
                                     wire:loading.attr="disabled" wire:target="delete, save"
                                     class="w-full sm:w-auto sm:ml-auto">
                            {{ $this->form->clinicMedicine ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            @endif
        </flux:fieldset>
    </form>
</div>
