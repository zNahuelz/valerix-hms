<?php

use Livewire\Component;
use App\Models\Medicine;
use App\Models\Presentation;
use App\Livewire\Forms\MedicineForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;

new class extends Component {

    public MedicineForm $form;
    public array $presentations = [];

    public function mount(?string $medicineId = null): void
    {
        if (!Presentation::exists()) {
            Session::flash('error', __('medicine.errors.creation_disabled_empty_presentations'));
            $this->redirectRoute('medicine.index');
            return;
        }
        $this->presentations = Presentation::select(['id', 'description'])->orderBy('description')->get()->toArray();
        if (!$medicineId) {
            $this->form->presentation_id = $this->presentations[0]['id'];
        }
        if ($medicineId) {
            if (!is_numeric($medicineId)) {
                $this->redirectWithError($medicineId);
                return;
            }

            $medicine = Medicine::withTrashed()->find((int)$medicineId);

            if (!$medicine) {
                $this->redirectWithError($medicineId);
                return;
            }

            $this->form->medicine = $medicine;
            $this->form->fill($medicine->toArray());
        }
    }

    protected function redirectWithError($medicineId): void
    {
        Session::flash('error', __('medicine.errors.not_found', ['id' => $medicineId]));
        $this->redirectRoute('medicine.index');
    }

    public function save()
    {
        $sanitized = $this->form->sanitized();
        $this->validate();
        if ($this->form->medicine) {
            $this->form->medicine->update($sanitized);
            Session::flash('success', __('medicine.updated', ['name' => $sanitized['name'], 'id' => $this->form->medicine->id]));
        } else {
            $medicine = Medicine::create($sanitized);
            Session::flash('success', __('medicine.created', ['name' => $sanitized['name'], 'id' => $medicine->id]));
        }
        return redirect()->to(route('medicine.index'));
    }

    public function delete()
    {
        if ($this->form->medicine) {
            if ($this->form->medicine->trashed()) {
                $this->form->medicine->restore();
                Session::flash('success', __('medicine.restored', ['id' => $this->form->medicine->id]));
            } else {
                $this->form->medicine->delete();
                Session::flash('success', __('medicine.deleted', ['id' => $this->form->medicine->id]));
            }
        }
        return redirect()->to(route('medicine.index'));
    }

    public function generateRandomBarcode(): void
    {
        $maxAttempts = 100;
        $barcode = '';
        for ($i = 0; $i < $maxAttempts; $i++) {
            $barcode = $this->barcodeFactory();
            if (!Medicine::where('barcode', $barcode)->first()) {
                $this->form->barcode = $barcode;
                return;
            }
        }
        Session::flash('error', __('medicine.errors.barcode_generation_failed'));
    }

    protected function barcodeFactory(): string
    {
        return str_pad(rand(1000000000000, 9999999999999), 13, '0', STR_PAD_LEFT);
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->medicine ? 'medicine.edit' : 'medicine.create')])
            ->title(__($this->form->medicine ? 'views.medicine.edit' : 'views.medicine.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-xl" wire:submit="save">
        @if($this->form->medicine && $this->form->medicine->trashed())
            <x-shared.alert type="info">{{ __('medicine.is_deleted') }}</x-shared.alert>
        @endif
        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-3" wire:loading.attr="disabled"
                       wire:target="save, delete">
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input wire:model.live.blur="form.name" type="text"/>
                <flux:error name="form.name"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.barcode') }}</flux:label>
                <flux:input.group>
                    <flux:input wire:model.live.blur="form.barcode" wire:loading.attr="disabled"
                                wire:target="generateRandomBarcode, delete, save" type="text"/>
                    <flux:button type="button" variant="primary" color="amber" wire:click="generateRandomBarcode"
                                 wire:loading.attr="disabled" wire:target="generateRandomBarcode ,delete, save"
                                 icon="circle-stack" title="{{__('common.randomize_barcode')}}">
                    </flux:button>
                </flux:input.group>
                <flux:error name="form.barcode"/>
            </flux:field>
            <div class="col-span-full">
                <flux:field>
                    <flux:label badge="{{ __('common.required') }}">{{ __('common.composition') }}</flux:label>
                    <flux:input wire:model.live.blur="form.composition" type="text"/>
                    <flux:error name="form.composition"/>
                </flux:field>
            </div>
            <div class="col-span-full">
                <flux:field>
                    <flux:label>{{ __('common.description') }}</flux:label>
                    <flux:input wire:model.live.blur="form.description" type="text"/>
                    <flux:error name="form.description"/>
                </flux:field>
            </div>
            <div class="col-span-full">
                <flux:field>
                    <flux:label>{{ trans_choice('presentation.presentation',1) }}</flux:label>
                    <flux:select wire:model.live.blur="form.presentation_id">
                        @foreach($presentations as $presentation)
                            <flux:select.option
                                value="{{$presentation['id']}}">{{$presentation['description']}}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.presentation_id"/>
                </flux:field>
            </div>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @if($this->form->medicine)
                        @canany(['sys.admin', 'medicine.delete', 'medicine.restore'])
                            <flux:button type="button" variant="primary"
                                         color="{{ $this->form->medicine->trashed() ? 'amber' : 'red' }}"
                                         wire:click="delete"
                                         class="w-full md:w-auto" wire:loading.attr="disabled"
                                         wire:target="generateRandomBarcode, delete, save">
                                {{ $this->form->medicine->trashed() ? __('common.restore') : __('common.delete') }}
                            </flux:button>
                        @endcanany
                    @endif
                    @canany(['sys.admin', 'medicine.create', 'medicine.update'])
                        <flux:button type="submit" variant="primary" class="w-full md:w-auto md:ml-auto"
                                     wire:loading.attr="disabled" wire:target="generateRandomBarcode, delete, save">
                            {{ $this->form->medicine ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
