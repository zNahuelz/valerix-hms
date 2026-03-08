<?php

use Livewire\Component;
use App\Models\BuyOrder;
use App\Enums\BuyOrderStatus;

new class extends Component {
    public ?BuyOrder $buyOrder = null;

    public function mount(?string $buyOrderId = null): void
    {
        if ($buyOrderId) {
            if (!is_numeric($buyOrderId)) {
                $this->redirectWithError($buyOrderId);
                return;
            }

            $buyOrder = BuyOrder::withTrashed()
                ->with([
                    'clinic',
                    'supplier',
                    'createdBy',
                    'updatedBy',
                    'buyOrderDetails.medicine.presentation',
                ])
                ->find((int)$buyOrderId);

            if (!$buyOrder) {
                $this->redirectWithError($buyOrderId);
                return;
            }

            $this->buyOrder = $buyOrder;
        }
    }


    protected function redirectWithError($buyOrderId): void
    {
        Session::flash('error', __('buy-order.errors.not_found', ['id' => $buyOrderId]));
        $this->redirectRoute('buyOrder.index');
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('buy-order.detail', ['id' => $this->buyOrder?->id])])
            ->title(__('views.buy_order.detail'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-3xl">

        @if ($this->buyOrder && $this->buyOrder->trashed())
            <x-shared.alert type="info">{{ __('buy-order.is_deleted_alt') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-2">

            <flux:field>
                <flux:label>{{ trans_choice('clinic.clinic', 1) }}</flux:label>
                <flux:input.group>
                    <flux:input readonly value="{{ $buyOrder->clinic->name ?? __('common.null') }}" type="text"/>
                    <flux:button type="button" variant="primary" color="cyan"
                                 icon="ellipsis-horizontal" wire:navigate
                                 href="{{route('clinic.detail',['clinicId' => $buyOrder->clinic->id])}}">
                    </flux:button>
                </flux:input.group>
            </flux:field>

            <flux:field>
                <flux:label>{{ trans_choice('supplier.supplier', 1) }}</flux:label>
                <flux:input.group>
                    <flux:input readonly value="{{ $buyOrder->supplier->name ?? __('common.null') }}" type="text"/>
                    <flux:button type="button" variant="primary" color="cyan"
                                 icon="ellipsis-horizontal" wire:navigate
                                 href="{{route('supplier.detail',['supplierId' => $buyOrder->supplier->id])}}">
                    </flux:button>
                </flux:input.group>
            </flux:field>

            {{-- Status --}}
            <flux:field>
                <flux:label>{{ __('common.status') }}</flux:label>
                <flux:input readonly
                            value="{{ BuyOrderStatus::tryFrom($buyOrder->getRawOriginal('status'))?->label() ?? $buyOrder->getRawOriginal('status') }}"
                            type="text"/>
            </flux:field>

            {{-- Subtotal --}}
            <flux:field>
                <flux:label>{{ __('common.subtotal') }}</flux:label>
                <flux:input readonly
                            value="{{ __('common.main_money_alt') . ' ' . number_format($buyOrder->subtotal, 2) }}"
                            type="text"/>
            </flux:field>

            {{-- Tax --}}
            <flux:field>
                <flux:label>{{ __('common.tax') }}</flux:label>
                <flux:input readonly value="{{ __('common.main_money_alt') . ' ' . number_format($buyOrder->tax, 2) }}"
                            type="text"/>
            </flux:field>

            {{-- Total --}}
            <flux:field>
                <flux:label>{{ __('common.total') }}</flux:label>
                <flux:input readonly
                            value="{{ __('common.main_money_alt') . ' ' . number_format($buyOrder->total, 2) }}"
                            type="text"/>
            </flux:field>

            {{-- Created by --}}
            <flux:input readonly
                        value="{{ $buyOrder->createdBy->username ?? __('common.inserted_by_null') }}"
                        label="{{ __('common.created_by') }}"
                        type="text"/>

            {{-- Updated by --}}
            <flux:input readonly
                        value="{{ $buyOrder->updatedBy->username ?? __('common.inserted_by_null') }}"
                        label="{{ __('common.updated_by') }}"
                        type="text"/>

            {{-- Created at --}}
            <flux:input readonly
                        value="{{ $buyOrder->created_at?->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.created_at') }}"
                        type="text"/>

            {{-- Updated at --}}
            <flux:input readonly
                        value="{{ $buyOrder->updated_at?->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.updated_at_alt') }}"
                        type="text"/>

            {{-- Deleted at --}}
            @if ($buyOrder->trashed())
                <flux:input readonly
                            value="{{ $buyOrder->deleted_at?->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                            label="{{ __('common.deleted_at') }}"
                            type="text"/>
            @endif

            {{-- Details table --}}
            <div class="col-span-full">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('common.id_alt') }}</flux:table.column>
                        <flux:table.column>{{ trans_choice('medicine.medicine', 1) }}</flux:table.column>
                        <flux:table.column>{{ trans_choice('presentation.presentation', 1) }}</flux:table.column>
                        <flux:table.column>{{ __('common.amount') }}</flux:table.column>
                        <flux:table.column>{{ __('common.unit_price') }}</flux:table.column>
                        <flux:table.column>{{ __('common.subtotal') }}</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($buyOrder->buyOrderDetails as $detail)
                            <flux:table.row class="hover:bg-accent-content/10">

                                <flux:table.cell class="font-mono text-sm text-zinc-400">
                                    {{ $detail->medicine_id }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $detail->medicine->name ?? __('common.null') }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $detail->medicine->presentation?->description ?? __('common.null') }}
                                </flux:table.cell>

                                <flux:table.cell class="tabular-nums">
                                    {{ $detail->amount }}
                                </flux:table.cell>

                                <flux:table.cell class="font-mono tabular-nums">
                                    {{ __('common.main_money_alt') }} {{ number_format($detail->unit_price, 2) }}
                                </flux:table.cell>

                                <flux:table.cell class="font-mono tabular-nums">
                                    {{ __('common.main_money_alt') }} {{ number_format($detail->amount * $detail->unit_price, 2) }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:button
                                        type="button"
                                        variant="ghost"
                                        icon="ellipsis-horizontal"
                                        size="sm"
                                        wire:navigate
                                        href="{{route('medicine.detail', ['medicineId' => $detail->medicine_id])}}"
                                    />
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="7" class="text-center text-lg md:text-xl font-light">
                                    {{ __('treatment.errors.no_medicines') }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @canany(['sys.admin', 'buyOrder.update'])
                        <flux:button
                            type="button"
                            variant="primary"
                            class="w-full md:w-auto md:ml-auto"
                            wire:navigate
                            href="{{ route('buyOrder.edit', ['buyOrderId' => $buyOrder->id]) }}"
                        >
                            {{ __('common.edit') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>

        </flux:fieldset>
    </div>
</div>
