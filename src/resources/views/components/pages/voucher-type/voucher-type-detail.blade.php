<?php

use Livewire\Component;
use App\Models\VoucherType;

new class extends Component {
    public ?VoucherType $voucherType = null;


    public function mount(?string $voucherTypeId = null): void
    {
        if ($voucherTypeId) {
            if (!is_numeric($voucherTypeId)) {
                $this->redirectWithError($voucherTypeId);
                return;
            }

            $voucherType = VoucherType::withTrashed()->find((int)$voucherTypeId);

            if (!$voucherType) {
                $this->redirectWithError($voucherTypeId);
                return;
            }

            $voucherType->load('voucherSeries');

            $this->voucherType = $voucherType;
        }
    }

    protected function redirectWithError($voucherTypeId)
    {
        Session::flash('error', __('voucher-type.errors.not_found', ['id' => $voucherTypeId]));
        $this->redirectRoute('voucherType.index');
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('voucher-type.detail', ['id' => $this->voucherType?->id,])])
            ->title(__('views.voucher_type.detail'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-3xl">
        @if($this->voucherType && $this->voucherType->trashed())
            <x-shared.alert type="info">{{ __('voucher-type.is_deleted_alt') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <flux:field>
                <flux:label>{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input readonly value="{{ $voucherType->name }}" type="text"/>
            </flux:field>
            <flux:input readonly
                        value="{{ $this->voucherType->created_at?->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.created_at') }}" type="text"/>
            <flux:input readonly
                        value="{{ $this->voucherType->updated_at?->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.updated_at_alt') }}" type="text"/>

            <div class="col-span-full">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('common.id_alt') }}</flux:table.column>
                        <flux:table.column>{{ __('voucher-serie.serie') }}</flux:table.column>
                        <flux:table.column>{{ __('common.next_value_alt') }}</flux:table.column>
                        <flux:table.column>{{ __('common.status') }}</flux:table.column>
                        <flux:table.column>{{ __('common.created_at') }}</flux:table.column>
                        <flux:table.column>{{ __('common.updated_at') }}</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($voucherType->voucherSeries as $voucherSerie)
                            <flux:table.row class="hover:bg-accent-content/10">
                                <flux:table.cell>{{ $voucherSerie->id }}</flux:table.cell>
                                <flux:table.cell>{{ $voucherSerie->serie }}</flux:table.cell>
                                <flux:table.cell>{{ $voucherSerie->next_value }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge color="{{ $voucherSerie->is_active ? 'green' : 'red' }}" size="sm"
                                                inset="top bottom">
                                        {{ $voucherSerie->is_active ? __('common.enabled_entity') : __('common.disabled_entity') }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>{{ $voucherSerie->created_at->timezone('America/Lima')->format('d/m/Y g:i A') }}</flux:table.cell>
                                <flux:table.cell>{{ $voucherSerie->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') }}</flux:table.cell>
                                <flux:table.cell>
                                    @canany(['sys.admin', 'voucherSerie.edit'])
                                        <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom"
                                                     title="{{ __('common.edit') }}"
                                                     href="{{ route('voucherSerie.edit', ['voucherSerieId' => $voucherSerie->id]) }}"
                                                     wire:navigate>
                                        </flux:button>
                                    @endcanany
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="7" class="text-center text-lg md:text-xl font-light">
                                    {{ __('voucher-serie.errors.empty_set') }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @canany(['sys.admin', 'voucherType.update'])
                        <flux:button type="button" variant="primary" class="w-full md:w-auto md:ml-auto" wire:navigate
                                     href="{{ route('voucherType.edit', ['voucherTypeId' => $voucherType->id]) }}">
                            {{ __('common.edit') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </div>
</div>
