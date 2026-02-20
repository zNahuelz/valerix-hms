<?php

use Livewire\Component;
use App\Models\Supplier;
use App\Livewire\Forms\SupplierForm;

new class extends Component {

    public SupplierForm $form;

    //TODO: Delete and restore.
    public function mount(?Supplier $supplier = null): void
    {
        if ($supplier) {
            $this->form->supplier = $supplier;
            $this->form->fill($supplier->toArray());
        }
    }

    public function save()
    {
        $this->validate();
        if ($this->form->supplier) {
            $this->form->supplier->update($this->form->all());
            session()->flash('success', __('supplier.updated', ['name' => $this->form->name, 'id' => $this->form->supplier->id]));
        } else {
            $supplier = Supplier::create($this->form->all());
            session()->flash('success', __('supplier.created', ['name' => $this->form->name, 'id' => $supplier->id]));
        }
        return redirect()->to('/dashboard/supplier');
    }

    public function render(): mixed
    {
        return $this->view()->layout('layouts::dashboard', ['heading' => __($this->form->supplier ? 'supplier.edit' : 'supplier.create')])->title(__($this->form->supplier ? 'views.supplier.edit' : 'views.supplier.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-3xl" wire:submit="save">
        @if($this->form->supplier && $this->form->supplier->deleted_at != null)
            <x-shared.alert type="warning">{{ __('supplier.is_deleted') }}</x-shared.alert>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input wire:model.live.blur="form.name" type="text" />
                <flux:error name="form.name" />
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ trans_choice('common.manager', 1) }}</flux:label>
                <flux:input wire:model.live.blur="form.manager" type="text" />
                <flux:error name="form.manager" />
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.ruc') }}</flux:label>
                <flux:input wire:model.live.blur="form.ruc" type="text" />
                <flux:error name="form.ruc" />
            </flux:field>

            <flux:input wire:model.live.blur="form.address" label="{{ __('common.address') }}" type="text" />
            <flux:input wire:model.live.blur="form.phone" label="{{ __('common.phone') }}" type="text" />
            <flux:input wire:model.live.blur="form.email" label="{{ __('common.email') }}" type="email" />
            <flux:input wire:model.live.blur="form.description" label="{{ __('common.description') }}" type="text" />

            <div class="col-span-full">
                <div class="flex flex-col items-end">
                    <flux:button type="submit" variant="primary" class="w-full md:w-auto">
                        {{ $this->form->supplier ? __('common.edit') : __('common.save') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </form>
</div>