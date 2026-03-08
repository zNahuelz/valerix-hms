<?php

use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\VoucherSerie;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination, WithoutUrlPagination;

    public string $keyword = '';
    public string $searchColumn = 'id';
    public string $statusFilter = 'enabled';
    public bool $searching = false;

    protected function rules(): array
    {
        $rules = [
            'id' => ['regex:/^\d+$/'],
            'serie' => ['required', 'string', 'min:2']
        ];
        return [
            'searchColumn' => ['required', 'in:id,serie'],
            'keyword' => $rules[$this->searchColumn] ?? ['string', 'min:3']
        ];
    }

    public function updatedKeyword(): void
    {
        $this->searching = false;
    }

    public function updatedSearchColumn(): void
    {
        $this->keyword = '';
        $this->searching = false;
        $this->resetValidation();
    }

    public function updatedStatusFilter(): void
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
        $this->reset();
        $this->searching = false;
        $this->resetValidation();
        $this->resetPage();
    }

    #[Computed]
    public function voucherSeries()
    {
        $query = VoucherSerie::query()->with('voucherType');

        if ($this->statusFilter) {
            switch ($this->statusFilter) {
                case 'enabled':
                    $query->where('is_active', true);
                    break;
                case 'disabled':
                    $query->where('is_active', false);
                    break;
                default:
                    break;
            }
        }

        if ($this->searching && $this->keyword) {
            switch ($this->searchColumn) {
                case 'id':
                    $query->where('id', $this->keyword);
                    break;
                case 'serie':
                    $query->whereLike('serie', "%{$this->keyword}%", caseSensitive: false);
                    break;
            }
        }

        return $query->orderBy('updated_at', 'desc')->paginate(20);
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('voucher-serie.index')])
            ->title(__('views.voucher_serie.index'));
    }
};
?>

<div>
    <div class="flex flex-col md:flex-row w-full items-stretch md:items-center justify-between gap-4 mb-2">
        @canany(['sys.admin', 'voucherSerie.create'])
            <flux:button variant="primary" icon="plus" wire:navigate href="{{ route('voucherSerie.create') }}"
                         class="w-full md:w-auto">
                {{ __('common.new') }}
            </flux:button>
        @endcanany
        <form wire:submit.prevent="search"
              class="flex flex-col md:flex-row items-stretch md:items-center gap-2 w-full md:w-auto md:ml-auto">
            <flux:select wire:model.live="searchColumn" class="w-full md:w-40">
                <flux:select.option value="id">{{ __('common.id') }}</flux:select.option>
                <flux:select.option
                    value="serie">{{ trans_choice('voucher-serie.voucher_serie', 1) }}</flux:select.option>
            </flux:select>
            <flux:input wire:model="keyword" :placeholder="__('common.search') . '...'" class="w-full md:w-64"/>
            <flux:button.group>
                <flux:button type="button" icon="arrow-path" square wire:click="resetSearch"
                             wire:loading.attr="disabled" wire:target="resetSearch" class="w-full md:w-auto p-3"/>
                <flux:button type="submit" icon="magnifying-glass" wire:loading.attr="disabled" wire:target="search"
                             class="w-full md:w-auto">
                    {{ __('common.search') }}
                </flux:button>
            </flux:button.group>
        </form>
    </div>
    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('common.id_alt') }}</flux:table.column>
            <flux:table.column>{{ trans_choice('voucher-type.voucher_type', 1) }}</flux:table.column>
            <flux:table.column>{{ __('voucher-serie.serie') }}</flux:table.column>
            <flux:table.column>{{ __('common.next_value_alt') }}</flux:table.column>
            <flux:table.column>{{ __('common.status') }}</flux:table.column>
            <flux:table.column>{{ __('common.created_at')  }}</flux:table.column>
            <flux:table.column>{{ __('common.updated_at') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse($this->voucherSeries as $voucherSerie)
                <flux:table.row class="hover:bg-accent-content/10">
                    <flux:table.cell>{{ $voucherSerie->id }}</flux:table.cell>
                    <flux:table.cell>{{ $voucherSerie->voucherType?->name ?? __('common.null') }}</flux:table.cell>
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
                    <flux:table.cell colspan="8" class="text-center text-lg md:text-xl font-light">
                        {{ __('voucher-serie.errors.empty_set') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    <x-shared.pagination :paginator="$this->voucherSeries"></x-shared.pagination>
    <div class="flex flex-col items-end mt-2">
        <flux:select wire:model.live="statusFilter" wire:loading.attr="disabled" class="w-full md:w-50">
            <flux:select.option value="all">{{ __('common.status_filter.all') }}</flux:select.option>
            <flux:select.option value="enabled">{{ __('common.status_filter.enabled') }}</flux:select.option>
            <flux:select.option value="disabled">{{ __('common.status_filter.disabled') }}</flux:select.option>
        </flux:select>
    </div>
</div>

