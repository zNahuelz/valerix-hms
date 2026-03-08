<?php

use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Enums\BuyOrderStatus;
use App\Models\BuyOrder;
use App\Models\Clinic;
use App\Models\Supplier;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination, WithoutUrlPagination;

    public string $searchColumn = 'id';
    public string $keyword = '';
    public string $clinicId = '';
    public string $supplierId = '';

    public string $visibilityFilter = 'visible';
    public bool $searching = false;

    public array $clinics = [];
    public array $suppliers = [];

    public function mount(): void
    {
        $this->clinics = Clinic::select(['id', 'name'])->whereNull('deleted_at')->orderBy('name')->get()->toArray();
        $this->suppliers = Supplier::select(['id', 'name'])->whereNull('deleted_at')->orderBy('name')->get()->toArray();

        $this->clinicId = (string)($this->clinics[0]['id'] ?? '');
        $this->supplierId = (string)($this->suppliers[0]['id'] ?? '');
    }

    protected function rules(): array
    {
        return match ($this->searchColumn) {
            'id' => ['keyword' => ['required', 'regex:/^\d+$/']],
            'clinic_id' => ['clinicId' => ['required', 'exists:clinics,id']],
            'supplier_id' => ['supplierId' => ['required', 'exists:suppliers,id']],
            default => [],
        };
    }

    public function updatedSearchColumn(): void
    {
        $this->keyword = '';
        $this->searching = false;
        $this->resetValidation();
        $this->resetPage();
    }

    public function updatedKeyword(): void
    {
        $this->searching = false;
    }

    public function updatedVisibilityFilter(): void
    {
        $this->resetPage();
    }

    public function search(): void
    {
        $this->keyword = trim($this->keyword);
        $this->validate();
        $this->searching = true;
        $this->resetPage();
    }

    public function resetSearch(): void
    {
        $this->reset(['keyword', 'clinicId', 'supplierId', 'searching', 'searchColumn']);
        $this->clinicId = (string)($this->clinics[0]['id'] ?? '');
        $this->supplierId = (string)($this->suppliers[0]['id'] ?? '');
        $this->resetValidation();
        $this->resetPage();
    }

    #[Computed]
    public function hasClinics(): bool
    {
        return count($this->clinics) > 0;
    }

    #[Computed]
    public function hasSuppliers(): bool
    {
        return count($this->suppliers) > 0;
    }

    #[Computed]
    public function buyOrders()
    {
        $query = BuyOrder::with(['clinic:id,name', 'supplier:id,name']);

        switch ($this->visibilityFilter) {
            case 'all':
                $query->withTrashed();
                break;
            case 'trashed':
                $query->onlyTrashed();
                break;
            default:
                break;
        }

        if ($this->searching) {
            switch ($this->searchColumn) {
                case 'id':
                    $query->where('id', $this->keyword);
                    break;
                case 'clinic_id':
                    $query->where('clinic_id', $this->clinicId);
                    break;
                case 'supplier_id':
                    $query->where('supplier_id', $this->supplierId);
                    break;
            }
        }

        return $query->orderBy('updated_at', 'desc')->paginate(12);
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('buy-order.index')])
            ->title(__('views.buy_order.index'));
    }
};
?>

<div>
    <div class="flex flex-col md:flex-row w-full items-stretch md:items-center justify-between gap-4 mb-2">
        @canany(['sys.admin', 'buyOrder.create'])
            <flux:button variant="primary" icon="plus" wire:navigate
                         href="{{ route('buyOrder.create') }}"
                         class="w-full md:w-auto">
                {{ __('common.new') }}
            </flux:button>
        @endcanany
        <form wire:submit.prevent="search"
              class="flex flex-col md:flex-row items-stretch md:items-center gap-2 w-full md:w-auto md:ml-auto">
            <flux:select wire:model.live="searchColumn" class="w-full md:w-44">
                <flux:select.option value="id">{{ __('common.id') }}</flux:select.option>
                <flux:select.option value="clinic_id">{{ trans_choice('clinic.clinic', 1) }}</flux:select.option>
                <flux:select.option value="supplier_id">{{ trans_choice('supplier.supplier', 1) }}</flux:select.option>
            </flux:select>
            @if ($searchColumn === 'id')
                <flux:input
                    wire:model="keyword"
                    type="number"
                    min="1"
                    :placeholder="__('common.search') . '...'"
                    class="w-full md:w-64"
                />
            @elseif ($searchColumn === 'clinic_id')
                @if ($this->hasClinics)
                    <flux:select wire:model.live="clinicId" class="w-full md:w-64">
                        @foreach ($clinics as $clinic)
                            <flux:select.option value="{{ $clinic['id'] }}">{{ $clinic['name'] }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @else
                    <flux:input readonly :placeholder="__('buy-order.errors.no_clinics_available')"
                                class="w-full md:w-64"/>
                @endif
                <flux:error name="clinicId"/>

            @elseif ($searchColumn === 'supplier_id')
                @if ($this->hasSuppliers)
                    <flux:select wire:model.live="supplierId" class="w-full md:w-64">
                        @foreach ($suppliers as $supplier)
                            <flux:select.option
                                value="{{ $supplier['id'] }}">{{ $supplier['name'] }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @else
                    <flux:input readonly :placeholder="__('buy-order.errors.no_suppliers_available')"
                                class="w-full md:w-64"/>
                @endif
                <flux:error name="supplierId"/>
            @endif
            @php
                $searchDisabled = ($searchColumn === 'clinic_id' && !$this->hasClinics)
                               || ($searchColumn === 'supplier_id' && !$this->hasSuppliers);
            @endphp
            <flux:button.group>
                <flux:button type="button" icon="arrow-path" square
                             wire:click="resetSearch"
                             wire:loading.attr="disabled"
                             wire:target="resetSearch"
                             class="w-full md:w-auto p-3"/>
                <flux:button type="submit" icon="magnifying-glass"
                             wire:loading.attr="disabled"
                             wire:target="search"
                             :disabled="$searchDisabled"
                             class="w-full md:w-auto">
                    {{ __('common.search') }}
                </flux:button>
            </flux:button.group>
        </form>
    </div>
    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('common.id_alt') }}</flux:table.column>
            <flux:table.column>{{ trans_choice('clinic.clinic', 1) }}</flux:table.column>
            <flux:table.column>{{ trans_choice('supplier.supplier', 1) }}</flux:table.column>
            <flux:table.column>{{ __('common.subtotal') }}</flux:table.column>
            <flux:table.column>{{ __('common.total') }}</flux:table.column>
            <flux:table.column>{{ __('common.status') }}</flux:table.column>
            <flux:table.column>{{ __('common.updated_at') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->buyOrders as $buyOrder)
                <flux:table.row class="hover:bg-accent-content/10">
                    <flux:table.cell>{{ $buyOrder->id }}</flux:table.cell>
                    <flux:table.cell>{{ $buyOrder->clinic->name ?? __('common.null') }}</flux:table.cell>
                    <flux:table.cell>{{ $buyOrder->supplier->name ?? __('common.null') }}</flux:table.cell>
                    <flux:table.cell class="font-mono tabular-nums">
                        {{ number_format($buyOrder->subtotal, 2) }}
                    </flux:table.cell>
                    <flux:table.cell class="font-mono tabular-nums">
                        {{ number_format($buyOrder->total, 2) }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            color="{{ $buyOrder->deleted_at ? 'red' : 'green' }}"
                            size="sm"
                            inset="top bottom"
                        >
                            {{ $buyOrder->deleted_at
                                ? __('common.disabled_entity')
                                : (BuyOrderStatus::tryFrom($buyOrder->getRawOriginal('status'))?->label() ?? $buyOrder->getRawOriginal('status')) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $buyOrder->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button.group>
                            @canany(['sys.admin', 'buyOrder.edit', 'buyOrder.delete', 'buyOrder.restore'])
                                <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom"
                                             title="{{ __('common.edit') }}"
                                             href="{{ route('buyOrder.edit', ['buyOrderId' => $buyOrder->id]) }}"
                                             wire:navigate>
                                </flux:button>
                            @endcanany
                            @canany(['sys.admin', 'buyOrder.detail'])
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"
                                             title="{{ __('common.details') }}"
                                             href="{{ route('buyOrder.detail', ['buyOrderId' => $buyOrder->id]) }}"
                                             wire:navigate>
                                </flux:button>
                            @endcanany
                        </flux:button.group>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center text-lg md:text-xl font-light">
                        {{ __('buy-order.errors.empty_set') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    <x-shared.pagination :paginator="$this->buyOrders"/>
    <div class="flex flex-col items-end mt-2">
        <flux:select wire:model.live="visibilityFilter"
                     wire:loading.attr="disabled"
                     class="w-full md:w-50">
            <flux:select.option value="all">{{ __('common.index_filter.all') }}</flux:select.option>
            <flux:select.option value="visible">{{ __('common.index_filter.only_visible') }}</flux:select.option>
            <flux:select.option value="trashed">{{ __('common.index_filter.trashed') }}</flux:select.option>
        </flux:select>
    </div>
</div>
