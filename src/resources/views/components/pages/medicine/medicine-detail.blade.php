<?php

use Livewire\Component;
use App\Models\Medicine;

new class extends Component {
    public ?Medicine $medicine = null;

    public function mount(?string $medicineId = null): void
    {
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

            $medicine->load(['createdBy', 'updatedBy', 'presentation']);

            $this->medicine = $medicine;
        }
    }

    protected function redirectWithError($medicineId): void
    {
        Session::flash('error', __('medicine.errors.not_found', ['id' => $medicineId]));
        $this->redirectRoute('medicine.index');
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('medicine.detail')])
            ->title(__('views.medicine.detail'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-2xl">
        @if($this->medicine && $this->medicine->trashed())
            <x-shared.alert type="info">{{ __('medicine.is_deleted_alt') }}</x-shared.alert>
        @endif
        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <flux:field>
                <flux:label>{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input readonly value="{{ $this->medicine->name }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.barcode') }}</flux:label>
                <flux:input readonly value="{{ $this->medicine->barcode }}" type="text"/>
            </flux:field>
            <div class="col-span-full">
                <flux:field>
                    <flux:label>{{ __('common.composition') }}</flux:label>
                    <flux:input readonly value="{{ $this->medicine->composition }}" type="text"/>
                </flux:field>
            </div>
            <div class="col-span-full">
                <flux:input readonly
                            value="{{ $this->medicine->description != '' ? $this->medicine->description : __('common.null') }}"
                            label="{{ __('common.description') }}"
                            type="text"/>
            </div>
            <div class="col-span-full">
                <flux:field>
                    <flux:label>{{ trans_choice('presentation.presentation',1) }}</flux:label>
                    <flux:input.group>
                        <flux:input readonly value="{{ $this->medicine->presentation->description  }}"
                                    type="text"/>
                        <flux:button type="button" variant="primary" color="cyan"
                                     icon="ellipsis-horizontal" wire:navigate
                                     href="{{route('presentation.detail',['presentationId' => $this->medicine->presentation->id])}}">
                        </flux:button>
                    </flux:input.group>
                </flux:field>
            </div>
            <flux:input readonly value="{{ $this->medicine->createdBy->username ?? __('common.inserted_by_null') }}"
                        label="{{ __('common.created_by') }}" type="text"/>
            <flux:input readonly value="{{ $this->medicine->updatedBy->username ?? __('common.inserted_by_null') }}"
                        label="{{ __('common.updated_by') }}" type="text"/>
            <flux:input readonly
                        value="{{ $medicine->created_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.created_at') }}" type="text"/>
            <flux:input readonly
                        value="{{ $medicine->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.updated_at_alt') }}" type="text"/>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @canany(['sys.admin', 'medicine.update', 'medicine.delete', 'medicine.restore'])
                        <flux:button type="button" variant="primary" class="w-full md:w-auto md:ml-auto" wire:navigate
                                     href="{{ route('medicine.edit', ['medicineId' => $this->medicine->id]) }}">
                            {{ __('common.edit') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </div>
</div>

