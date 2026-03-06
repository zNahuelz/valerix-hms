<?php

use Livewire\Component;
use App\Models\Treatment;

new class extends Component
{
    public ?Treatment $treatment = null;

    public function mount(?string $treatmentId = null): void
    {
        if ($treatmentId) {
            if (!is_numeric($treatmentId)) {
                $this->redirectWithError($treatmentId);
                return;
            }

            $treatment = Treatment::withTrashed()->find((int)$treatmentId);

            if (!$treatment) {
                $this->redirectWithError($treatmentId);
                return;
            }

            //$treatment->load('medicines');
            $treatment->load('medicines.presentation');

            $this->treatment = $treatment;
        }
    }

    protected function redirectWithError($treatmentId)
    {
        Session::flash('error', __('treatment.errors.not_found', ['id' => $treatmentId]));
        $this->redirectRoute('treatment.index');
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('treatment.detail', ['id' => $this->treatment?->id, 'name' => ucwords(strtolower($this->treatment->name))])])
            ->title(__('views.treatment.detail'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-3xl">
        @if($this->treatment && $this->treatment->trashed())
            <x-shared.alert type="info">{{ __('treatment.is_deleted_alt') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <flux:field class="col-span-full">
                <flux:label>{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input readonly value="{{ $treatment->name }}" type="text"/>
            </flux:field>
            <flux:field class="col-span-full">
                <flux:label>{{ __('common.description') }}</flux:label>
                <flux:input readonly value="{{ $treatment->description ?? __('common.null') }}" type="text"/>
            </flux:field>
            <flux:field class="col-span-full">
                <flux:label>{{ __('common.procedure') }}</flux:label>
                <flux:input readonly value="{{ $treatment->procedure ?? __('common.null') }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.price') }}</flux:label>
                <flux:input readonly value="{{ __('common.main_money_alt').' '.$treatment->price }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.tax') }}</flux:label>
                <flux:input readonly value="{{ __('common.main_money_alt').' '.$treatment->tax }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.profit') }}</flux:label>
                <flux:input readonly value="{{ __('common.main_money_alt').' '.$treatment->profit }}" type="text"/>
            </flux:field>

            <flux:input readonly value="{{ $treatment->createdBy->username ?? __('common.inserted_by_null') }}"
                        label="{{ __('common.created_by') }}" type="text"/>
            <flux:input readonly value="{{ $treatment->updatedBy->username ?? __('common.inserted_by_null') }}"
                        label="{{ __('common.updated_by') }}" type="text"/>
            <flux:input readonly
                        value="{{ $this->treatment->created_at?->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.created_at') }}" type="text"/>
            <flux:input readonly
                        value="{{ $this->treatment->updated_at?->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.updated_at_alt') }}" type="text"/>

            <div class="col-span-full">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ trans_choice('common.name',1) }}</flux:table.column>
                        <flux:table.column>{{ __('common.barcode') }}</flux:table.column>
                        <flux:table.column>{{ trans_choice('presentation.presentation',1) }}</flux:table.column>
                        <flux:table.column/>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($treatment->medicines as $medicine)
                            <flux:table.row class="hover:bg-accent-content/10">
                                <flux:table.cell>{{ $medicine->name }}</flux:table.cell>
                                <flux:table.cell class="font-mono text-sm text-zinc-400">
                                    {{ $medicine->barcode ?? __('common.null') }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $medicine->presentation?->description ?? __('common.null') }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:button
                                        type="button"
                                        variant="ghost"
                                        icon="ellipsis-horizontal"
                                        size="sm"
                                        wire:navigate
                                        href="{{route('medicine.detail', ['medicineId' => $medicine->id])}}"
                                    />
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4" class="text-center text-lg md:text-xl font-light">
                                    {{ __('treatment.errors.no_medicines') }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @canany(['sys.admin', 'treatment.update'])
                        <flux:button type="button" variant="primary" class="w-full md:w-auto md:ml-auto" wire:navigate
                                     href="{{ route('treatment.edit', ['treatmentId' => $treatment->id]) }}">
                            {{ __('common.edit') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </div>
</div>
