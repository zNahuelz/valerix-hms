<?php

use App\Livewire\Forms\TreatmentForm;
use App\Models\Medicine;
use App\Models\Treatment;
use Livewire\Component;

new class extends Component
{
    public TreatmentForm $form;

    public int    $step           = 1;
    public int    $totalSteps     = 2;
    public bool   $isEditing      = false;

    public string $barcodeInput   = '';
    public ?array $scannedMedicine = null;
    public string $barcodeError   = '';

    public array $resolvedMedicines = [];

    public function mount(?string $treatmentId = null): void
    {
        if($treatmentId){
            if (!is_numeric($treatmentId)) {
                $this->redirectWithError($treatmentId);
                return;
            }

            $treatment = Treatment::withTrashed()->with('medicines.presentation')->find((int)$treatmentId);

            if (!$treatment) {
                $this->redirectWithError($treatmentId);
                return;
            }
            $this->isEditing = true;
            $this->form->treatment = $treatment;
            $this->form->fill($treatment->toArray());
            $this->form->medicines     = $treatment->medicines->pluck('id')->toArray();
            $this->resolvedMedicines   = $treatment->medicines->toArray();
        }
    }

    protected function redirectWithError($treatmentId)
    {
        Session::flash('error', __('treatment.errors.not_found', ['id' => $treatmentId]));
        $this->redirectRoute('treatment.index');
    }

    public function updatedFormPrice(): void
    {
        $this->form->recalculateTax();
    }

    public function updatedFormProfit(): void
    {
        $this->form->recalculateTax();
    }

    public function nextStep(): void
    {
        $this->form->validateStep($this->step);
        $this->step++;
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function scanBarcode(): void
    {
        $this->barcodeError   = '';
        $this->scannedMedicine = null;

        $barcode = trim($this->barcodeInput);

        if (blank($barcode)) {
            $this->barcodeError = __('validation.medicines.barcode_empty');
            return;
        }

        $medicine = Medicine::withTrashed()->where('barcode', $barcode)->first();

        if (!$medicine) {
            $this->barcodeError = __('validation.medicines.barcode_not_found');
            return;
        }

        if($medicine->trashed())
        {
            Session::flash('info',__('treatment.errors.medicine_trashed', ['name' => $medicine->name, 'id' => $medicine->id ]));
        }

        $medicine->load('presentation');

        if (in_array($medicine->id, $this->form->medicines)) {
            $this->barcodeError = __('validation.medicines.already_added');
            return;
        }

        $this->form->medicines[]  = $medicine->id;
        $this->resolvedMedicines[] = $medicine->toArray();
        $this->barcodeInput        = '';
    }


    public function removeMedicine(int $medicineId): void
    {
        $this->form->medicines     = array_values(
            array_filter($this->form->medicines, fn($id) => $id !== $medicineId)
        );
        $this->resolvedMedicines   = array_values(
            array_filter($this->resolvedMedicines, fn($m) => $m['id'] !== $medicineId)
        );
    }

    public function save()
    {
        $this->form->validateStep(2);

        $data      = $this->form->sanitized();
        $medicines = $data['medicines'];
        unset($data['medicines']);

        if ($this->isEditing) {
            $this->form->treatment->update($data);
            $this->form->treatment->medicines()->sync($medicines);
            Session::flash('success',__('treatment.updated', ['id' => $this->form->treatment->id, 'name' => $this->form->treatment->name]));
        } else {
            $treatment = Treatment::create($data);
            $treatment->medicines()->sync($medicines);
            Session::flash('success',__('treatment.created', ['name' => $treatment->name, 'id' => $treatment->id]));
        }

        return redirect()->to(route('treatment.index'));
    }

    public function delete()
    {
        if ($this->form->treatment) {
            if ($this->form->treatment->trashed()) {
                $this->form->treatment->restore();
                Session::flash('success', __('treatment.restored', ['id' => $this->form->treatment->id]));
            } else {
                $this->form->treatment->delete();
                Session::flash('success', __('treatment.deleted', ['id' => $this->form->treatment->id]));
            }
        }
        return redirect()->to(route('treatment.index'));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->isEditing ? 'treatment.edit' : 'treatment.create')])
            ->title(__($this->form->treatment ? 'views.treatment.edit' : 'views.treatment.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-3xl">
        @if($this->form->treatment && $this->form->treatment->trashed())
            <x-shared.alert type="info">{{ __('treatment.is_deleted') }}</x-shared.alert>
        @endif
        {{-- Step badge --}}
        <div class="my-4 text-center md:text-end">
            <flux:badge size="lg" color="emerald">
                <span class="font-bold uppercase">
                    {{ __('common.step_one_of', ['step' => $step, 'total' => $totalSteps]) }}
                </span>
            </flux:badge>
        </div>
        {{-- ────────────────── STEP 1: Treatment Info ────────────────── --}}
        @if ($step === 1)
            <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="col-span-full">
                    <flux:field>
                        <flux:label badge="{{ __('common.required') }}">{{ trans_choice('common.name', 1) }}</flux:label>
                        <flux:input wire:model.live.blur="form.name" type="text"/>
                        <flux:error name="form.name"/>
                    </flux:field>
                </div>
                <div class="col-span-full">
                    <flux:field>
                        <flux:label>{{ __('common.description') }}</flux:label>
                        <flux:input wire:model.live.blur="form.description" type="text"/>
                        <flux:error name="form.description"/>
                    </flux:field>
                </div>
                <flux:field class="col-span-full">
                    <flux:label>{{ __('common.procedure') }}</flux:label>
                    <flux:input wire:model.live.blur="form.procedure" type="text"/>
                    <flux:error name="form.procedure"/>
                </flux:field>
                <flux:field>
                    <flux:label badge="{{ __('common.required') }}">{{ __('common.price') }}</flux:label>
                    <flux:input wire:model.live.blur="form.price" type="number" min="1" step="0.01"/>
                    <flux:error name="form.price"/>
                </flux:field>
                <flux:field>
                    <flux:label badge="{{ __('common.required') }}">{{ __('common.profit') }}</flux:label>
                    <flux:input wire:model.live.blur="form.profit" type="number" min="0" step="0.01"/>
                    <flux:error name="form.profit"/>
                </flux:field>

                {{-- Tax (read-only, auto-calculated) --}}
                <flux:field class="col-span-full">
                    <flux:label>
                        {{ __('common.tax') }}
                        <span class="text-xs text-zinc-400 font-normal ml-1">{{ __('common.autogenerated') }}</span>
                    </flux:label>
                    <flux:input value="{{ $form->tax }}" type="number" readonly disabled/>
                </flux:field>

                <div class="flex flex-col md:flex-row md:justify-between gap-2 pt-2 col-span-full">
                    @if($this->form->treatment)
                        @canany(['sys.admin', 'treatment.delete', 'treatment.restore'])
                            <flux:button type="button" variant="primary"
                                         color="{{ $this->form->treatment->trashed() ? 'amber' : 'red' }}"
                                         wire:click="delete"
                                         class="w-full md:w-auto" wire:loading.attr="disabled"
                                         wire:target="delete, save">
                                {{ $this->form->treatment->trashed() ? __('common.restore') : __('common.delete') }}
                            </flux:button>
                        @endcanany
                    @endif
                    <flux:button type="button" variant="primary" wire:click="nextStep">
                        {{ __('common.next') }}
                    </flux:button>
                </div>

            </flux:fieldset>
        @endif

        {{-- ────────────────── STEP 2: Medicines ────────────────── --}}
        @if ($step === 2)
            <flux:fieldset class="space-y-4">
                <flux:legend>{{ trans_choice('medicine.medicine',2)  }}</flux:legend>

                {{-- Barcode scanner --}}
                <flux:field>
                    <flux:label>{{ __('common.barcode') }}</flux:label>
                    <flux:input.group>
                        <flux:input
                            wire:model.live="barcodeInput"
                            wire:keydown.enter="scanBarcode"
                            type="text"
                            placeholder="{{ __('common.barcode_placeholder') }}"
                            autofocus
                        />
                        <flux:button
                            type="button"
                            variant="primary"
                            color="cyan"
                            icon="plus"
                            wire:click="scanBarcode"
                        />
                    </flux:input.group>
                    @if ($barcodeError)
                        <flux:error>{{ $barcodeError }}</flux:error>
                    @endif
                </flux:field>

                {{-- Medicines table --}}
                @if (count($resolvedMedicines) > 0)
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ trans_choice('common.name',1) }}</flux:table.column>
                            <flux:table.column>{{ __('common.barcode') }}</flux:table.column>
                            <flux:table.column>{{ trans_choice('presentation.presentation',1) }}</flux:table.column>
                            <flux:table.column/>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($resolvedMedicines as $medicine)
                                <flux:table.row :key="$medicine['id']">
                                    <flux:table.cell>{{ $medicine['name'] }}</flux:table.cell>
                                    <flux:table.cell class="font-mono text-sm text-zinc-400">
                                        {{ $medicine['barcode'] ?? '—' }}
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        {{ $medicine['presentation']['description'] ?? __('common.null') }}
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:button
                                            type="button"
                                            variant="ghost"
                                            icon="trash"
                                            size="sm"
                                            wire:click="removeMedicine({{ $medicine['id'] }})"
                                        />
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                    <p class="text-xs text-zinc-400">
                        {{ count($resolvedMedicines) >= 1 ? __('treatment.selected_medicines', ['count' => count($resolvedMedicines)]) : '' }}
                    </p>
                @else
                    <flux:callout icon="beaker" color="zinc">
                        <flux:callout.heading>{{ __('treatment.errors.no_medicines') }}</flux:callout.heading>
                        <flux:callout.text>{{ __('treatment.errors.no_medicines_hint') }}</flux:callout.text>
                    </flux:callout>
                @endif

                <flux:error name="form.medicines"/>

                {{-- Step 2 footer --}}
                <div class="flex flex-col md:flex-row md:justify-between gap-2 pt-2">
                    <flux:button type="button" wire:click="prevStep">
                        {{ __('common.back') }}
                    </flux:button>

                    @canany(['sys.admin', 'treatment.create', 'treatment.edit'])
                        <flux:button type="button" variant="primary" wire:click="save">
                            {{ $isEditing ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </flux:fieldset>
        @endif

    </div>
</div>
