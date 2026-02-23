<?php

use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Presentation;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination, WithoutUrlPagination;

    public $keyword = '';
    public $searchColumn = 'id';
    public $visibilityFilter = 'visible';
    public $searching = false;

    protected function rules(): array
    {
        $rules = [
            'id' => ['regex:/^\d+$/'],
            'name' => ['string', 'min:3'],
            'description' => ['string', 'min:3'],
        ];
        return [
            'searchColumn' => ['required', 'in:id,name,description'],
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
        $this->reset();
        $this->searching = false;
        $this->resetValidation();
        $this->resetPage();
    }

    #[Computed]
    public function presentations()
    {
        $query = Presentation::query();

        if ($this->visibilityFilter) {
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
        }

        if ($this->searching && $this->keyword) {
            switch ($this->searchColumn) {
                case 'id':
                    $query->where('id', $this->keyword);
                    break;
                case 'name':
                    $query->whereLike('name', "%{$this->keyword}%", caseSensitive: false);
                    break;
                case 'description':
                    $query->whereLike('description', "%{$this->keyword}%", caseSensitive: false);
                    break;
            }
        }

        return $query->orderBy('updated_at', 'desc')->paginate(12);
    }


    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('presentation.index')])
            ->title(__('views.presentation.index'));
    }
};
?>

<div>
    <div class="flex flex-col md:flex-row w-full items-stretch md:items-center justify-between gap-4 mb-2">
        @canany(['sys.admin', 'presentation.create'])
            <flux:button variant="primary" icon="plus" wire:navigate href="{{ route('presentation.create') }}"
                         class="w-full md:w-auto">
                {{ __('common.new') }}
            </flux:button>
        @endcanany
        <form wire:submit.prevent="search"
              class="flex flex-col md:flex-row items-stretch md:items-center gap-2 w-full md:w-auto md:ml-auto">
            <flux:select wire:model.live="searchColumn" class="w-full md:w-40">
                <flux:select.option value="id">{{ __('common.id') }}</flux:select.option>
                <flux:select.option value="name">{{ trans_choice('common.name', 1) }}</flux:select.option>
                <flux:select.option value="description">{{ __('common.description') }}</flux:select.option>
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
            <flux:table.column>{{ trans_choice('common.name', 1) }}</flux:table.column>
            <flux:table.column>{{ __('common.description') }}</flux:table.column>
            <flux:table.column>{{ __('common.numeric_value')  }}</flux:table.column>
            <flux:table.column>{{ __('common.status') }}</flux:table.column>
            <flux:table.column>{{ __('common.updated_at') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse($this->presentations as $presentation)
                <flux:table.row class="hover:bg-accent-content/10">
                    <flux:table.cell>{{ $presentation->id }}</flux:table.cell>
                    <flux:table.cell>{{ $presentation->name }}</flux:table.cell>
                    <flux:table.cell>{{ $presentation->description }}</flux:table.cell>
                    <flux:table.cell>{{ $presentation->numeric_value ?? '------' }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="{{ $presentation->deleted_at ? 'red' : 'green' }}" size="sm"
                                    inset="top bottom">
                            {{ $presentation->deleted_at ? __('common.disabled_entity') : __('common.enabled_entity') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $presentation->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button.group>
                            @canany(['sys.admin', 'presentation.edit', 'presentation.delete', 'presentation.restore'])
                                <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom"
                                             title="{{ __('common.edit') }}"
                                             href="{{ route('presentation.edit', ['presentationId' => $presentation->id]) }}"
                                             wire:navigate>
                                </flux:button>
                            @endcanany
                            @canany(['sys.admin', 'presentation.detail'])
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"
                                             title="{{ __('common.details') }}"
                                             href="{{ route('presentation.detail', ['presentationId' => $presentation->id]) }}"
                                             wire:navigate>
                                </flux:button>
                            @endcanany
                        </flux:button.group>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center text-lg md:text-xl font-light">
                        {{ __('presentation.errors.empty_set') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    <x-shared.pagination :paginator="$this->presentations"></x-shared.pagination>
    <div class="flex flex-col items-end mt-2">
        <flux:select wire:model.live="visibilityFilter" wire:loading.attr="disabled" class="w-full md:w-50">
            <flux:select.option value="all">{{ __('common.index_filter.all') }}</flux:select.option>
            <flux:select.option value="visible">{{ __('common.index_filter.only_visible') }}</flux:select.option>
            <flux:select.option value="trashed">{{ __('common.index_filter.trashed') }}</flux:select.option>
        </flux:select>
    </div>
</div>
