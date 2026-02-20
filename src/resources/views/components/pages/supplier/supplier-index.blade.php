<?php

use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Supplier;
use Livewire\Attributes\Computed;
use Illuminate\Support\Carbon;

new class extends Component {
    use WithPagination, WithoutUrlPagination;
    //Todo: Hide and show deleted records.
    public $keyword = '';
    public $searchColumn = 'id';

    protected function rules(): array
    {
        $rules = [
            'id' => ['numeric', 'min:1'],
            'ruc' => ['numeric', 'digits:11'],
            'name' => ['string', 'min:3']
        ];
        return [
            'keyword' => $rules[$this->searchColumn] ?? 'nullable|string'
        ];
    }

    public function updatedKeyword($value): void
    {
        $this->keyword = trim($value);
    }

    public function updatedSearchColumn(): void
    {
        $this->keyword = '';
        $this->resetValidation();
        $this->resetPage();
    }

    public function search(): void
    {
        $this->validate();
        $this->resetPage();
    }

    public function resetSearch(): void
    {
        $this->reset();
        $this->resetValidation();
        $this->resetPage();
    }

    #[Computed]
    public function suppliers()
    {
        $query = Supplier::query();

        if ($this->keyword) {
            switch ($this->searchColumn) {
                case 'id':
                    $query->where('id', $this->keyword);
                    break;
                case 'name':
                    $query->where('name', 'ilike', "%{$this->keyword}%");
                    break;
                case 'ruc':
                    $query->where('ruc', 'like', "%{$this->keyword}%");
                    break;
            }
        }

        return $query->orderBy('updated_at', 'desc')->paginate(10);
    }

    public function render(): mixed
    {
        return $this->view()->layout('layouts::dashboard', ['heading' => __('supplier.index')])->title(__('views.supplier.index'));
    }
};
?>

<div>
    <div class="flex flex-col md:flex-row w-full items-stretch md:items-center justify-between gap-4 mb-2">
        <flux:button variant="primary" icon="plus" wire:navigate href="/dashboard/supplier/create"
            class="w-full md:w-auto">
            {{ __('common.new') }}
        </flux:button>
        <form wire:submit.prevent="search"
            class="flex flex-col md:flex-row items-stretch md:items-center gap-2 w-full md:w-auto">
            <flux:select wire:model.live.blur="searchColumn" class="w-full md:w-40">
                <flux:select.option value="id">{{ __('common.id') }}</flux:select.option>
                <flux:select.option value="name">{{ trans_choice('common.name', 1) }}</flux:select.option>
                <flux:select.option value="ruc">{{ __('common.ruc') }}</flux:select.option>
            </flux:select>
            <flux:input wire:model.defer="keyword" :placeholder="__('common.search') . '...'" class="w-full md:w-64" />
            <flux:button.group>
                <flux:button type="button" icon="arrow-path" square wire:click="resetSearch"
                    wire:loading.attr="disabled" wire:target="resetSearch" class="w-full md:w-auto p-3" />
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
            <flux:table.column>{{ trans_choice('common.name', 1) }}</flux:table.column>
            <flux:table.column>{{ __('common.ruc') }}</flux:table.column>
            <flux:table.column>{{ __('common.phone')  }}</flux:table.column>
            <flux:table.column>{{ trans_choice('common.manager', 1) }}</flux:table.column>
            <flux:table.column>{{ __('common.status') }}</flux:table.column>
            <flux:table.column>{{ __('common.updated_at') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach($this->suppliers as $supplier)
                <flux:table.row class="hover:bg-accent-content/10">
                    <flux:table.cell>{{ $supplier->id }}</flux:table.cell>
                    <flux:table.cell>{{ $supplier->name }}</flux:table.cell>
                    <flux:table.cell>{{ $supplier->ruc }}</flux:table.cell>
                    <flux:table.cell>{{ $supplier->phone ?? '------' }}</flux:table.cell>
                    <flux:table.cell>{{ $supplier->manager }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="{{ $supplier->deleted_at ? 'red' : 'green' }}" size="sm" inset="top bottom">
                            {{ $supplier->deleted_at ? __('common.disabled_entity') : __('common.enabled_entity') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $supplier->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom"
                            href="/dashboard/supplier/{{ $supplier->id }}/edit" wire:navigate>
                        </flux:button>
                    </flux:table.cell>

                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
    <x-pagination :paginator="$this->suppliers"></x-pagination>
</div>