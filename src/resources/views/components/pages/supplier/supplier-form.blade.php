<?php

use Livewire\Component;
use App\Models\Supplier;
use App\Livewire\Forms\SupplierForm;
use Illuminate\Support\Facades\Session;

new class extends Component {

    public SupplierForm $form;

    public function mount(?string $supplierId = null): void
    {
        if ($supplierId) {
            if (!is_numeric($supplierId)) {
                $this->redirectWithError($supplierId);
                return;
            }

            $supplier = Supplier::withTrashed()->find((int)$supplierId);

            if (!$supplier) {
                $this->redirectWithError($supplierId);
                return;
            }

            $this->form->supplier = $supplier;
            $this->form->fill($supplier->toArray());
        }
    }

    protected function redirectWithError($supplierId)
    {
        Session::flash('error', __('supplier.errors.not_found', ['id' => $supplierId]));
        $this->redirectRoute('supplier.index');
    }

    public function save()
    {
        $sanitized = $this->form->sanitized();
        $this->validate();
        if ($this->form->supplier) {
            $this->form->supplier->update($sanitized);
            Session::flash('success', __('supplier.updated', ['name' => $sanitized['name'], 'id' => $this->form->supplier->id]));
        } else {
            $supplier = Supplier::create($sanitized);
            Session::flash('success', __('supplier.created', ['name' => $sanitized['name'], 'id' => $supplier->id]));
        }
        return redirect()->to(route('supplier.index'));
    }

    public function delete()
    {
        if ($this->form->supplier) {
            if ($this->form->supplier->trashed()) {
                $this->form->supplier->restore();
                Session::flash('success', __('supplier.restored', ['id' => $this->form->supplier->id]));
            } else {
                $this->form->supplier->delete();
                Session::flash('success', __('supplier.deleted', ['id' => $this->form->supplier->id]));
            }
        }
        return redirect()->to(route('supplier.index'));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->supplier ? 'supplier.edit' : 'supplier.create')])
            ->title(__($this->form->supplier ? 'views.supplier.edit' : 'views.supplier.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-3xl" wire:submit="save">
        @if($this->form->supplier && $this->form->supplier->trashed())
            <x-shared.alert type="info">{{ __('supplier.is_deleted') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-3" wire:loading.attr="disabled"
                       wire:target="save, delete">
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input wire:model.live.blur="form.name" type="text"/>
                <flux:error name="form.name"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ trans_choice('common.manager', 1) }}</flux:label>
                <flux:input wire:model.live.blur="form.manager" type="text"/>
                <flux:error name="form.manager"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.ruc') }}</flux:label>
                <flux:input wire:model.live.blur="form.ruc" type="text"/>
                <flux:error name="form.ruc"/>
            </flux:field>
            <flux:input wire:model.live.blur="form.address" label="{{ __('common.address') }}" type="text"/>
            <flux:input wire:model.live.blur="form.phone" label="{{ __('common.phone') }}" type="text"/>
            <flux:input wire:model.live.blur="form.email" label="{{ __('common.email') }}" type="email"/>
            <flux:input wire:model.live.blur="form.description" label="{{ __('common.description') }}" type="text"/>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @if($this->form->supplier)
                        @canany(['sys.admin', 'supplier.delete', 'supplier.restore'])
                            <flux:button type="button" variant="primary"
                                         color="{{ $this->form->supplier->trashed() ? 'amber' : 'red' }}"
                                         wire:click="delete"
                                         class="w-full md:w-auto" wire:loading.attr="disabled"
                                         wire:target="delete, save">
                                {{ $this->form->supplier->trashed() ? __('common.restore') : __('common.delete') }}
                            </flux:button>
                        @endcanany
                    @endif
                    @canany(['sys.admin', 'supplier.create', 'supplier.update'])
                        <flux:button type="submit" variant="primary" class="w-full md:w-auto md:ml-auto"
                                     wire:loading.attr="disabled" wire:target="delete, save">
                            {{ $this->form->supplier ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
