<?php

use Livewire\Component;
use App\Models\Supplier;

new class extends Component {
    public ?Supplier $supplier = null;

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

            $supplier->load(['createdBy', 'updatedBy']);

            $this->supplier = $supplier;
        }
    }

    protected function redirectWithError($supplierId)
    {
        Session::flash('error', __('supplier.errors.not_found', ['id' => $supplierId]));
        $this->redirectRoute('supplier.index');
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('supplier.detail', ['id' => $this->supplier->id, 'name' => ucwords(strtolower($this->supplier->name))])])
            ->title(__('views.supplier.detail'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-3xl">
        @if($this->supplier && $this->supplier->trashed())
            <x-shared.alert type="info">{{ __('supplier.is_deleted_alt') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <flux:field>
                <flux:label>{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input readonly value="{{ $this->supplier->name }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ trans_choice('common.manager', 1) }}</flux:label>
                <flux:input readonly value="{{ $this->supplier->manager }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.ruc') }}</flux:label>
                <flux:input readonly value="{{ $this->supplier->ruc }}" type="text"/>
            </flux:field>
            <flux:input readonly value="{{ $this->supplier->address }}" label="{{ __('common.address') }}"
                        type="text"/>
            <flux:input readonly value="{{ $this->supplier->phone }}" label="{{ __('common.phone') }}" type="text"/>
            <flux:input readonly value="{{ $this->supplier->email }}" label="{{ __('common.email') }}" type="email"/>
            <div class="col-span-full">
                <flux:input readonly value="{{ $this->supplier->description }}" label="{{ __('common.description') }}"
                            type="text"/>
            </div>
            <flux:input readonly value="{{ $this->supplier->createdBy->username ?? __('common.inserted_by_null') }}"
                        label="{{ __('common.created_by') }}" type="text"/>
            <flux:input readonly value="{{ $this->supplier->updatedBy->username ?? __('common.inserted_by_null') }}"
                        label="{{ __('common.updated_by') }}" type="text"/>
            <flux:input readonly
                        value="{{ $supplier->created_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.created_at') }}" type="text"/>
            <flux:input readonly
                        value="{{ $supplier->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.updated_at_alt') }}" type="text"/>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @canany(['sys.admin', 'supplier.update', 'supplier.delete', 'supplier.restore'])
                        <flux:button type="button" variant="primary" class="w-full md:w-auto md:ml-auto" wire:navigate
                                     href="{{ route('supplier.edit', ['supplierId' => $this->supplier->id]) }}">
                            {{ __('common.edit') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </div>
</div>
