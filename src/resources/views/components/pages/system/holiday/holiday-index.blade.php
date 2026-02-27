<?php

use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Holiday;
use Livewire\Attributes\Computed;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;

    public string $keyword = '';
    public string $searchColumn = 'id';
    public string $visibilityFilter = 'visible';
    public bool $searching = false;

    protected function rules(): array
    {
        $rules = [
            'id' => ['regex:/^\d+$/'],
            'name' => ['required','string', 'min:3']
        ];
        return [
            'searchColumn' => ['required', 'in:id,name'],
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
    public function holidays()
    {
        $query = Holiday::query();

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
            }
        }

        return $query->with(['createdBy','updatedBy'])->orderBy('updated_at', 'desc')->paginate(12);
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('holiday.index')])
            ->title(__('views.holiday.index'));
    }
};
?>

<div>
    <div class="flex flex-col md:flex-row w-full items-stretch md:items-center justify-between gap-4 mb-2">
        @canany(['sys.admin', 'holiday.create'])
            <flux:button variant="primary" icon="plus" wire:navigate href="{{ route('holiday.create') }}"
                         class="w-full md:w-auto">
                {{ __('common.new') }}
            </flux:button>
        @endcanany
        <form wire:submit.prevent="search"
              class="flex flex-col md:flex-row items-stretch md:items-center gap-2 w-full md:w-auto md:ml-auto">
            <flux:select wire:model.live="searchColumn" class="w-full md:w-40">
                <flux:select.option value="id">{{ __('common.id') }}</flux:select.option>
                <flux:select.option value="name">{{ trans_choice('common.name', 1) }}</flux:select.option>
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
            <flux:table.column>{{ __('common.date') }}</flux:table.column>
            <flux:table.column>{{ __('common.is_recurring') }}</flux:table.column>
            <flux:table.column>{{ __('common.created_by') }}</flux:table.column>
            <flux:table.column>{{ __('common.created_at') }}</flux:table.column>
            <flux:table.column>{{ __('common.updated_by') }}</flux:table.column>
            <flux:table.column>{{ __('common.updated_at') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse($this->holidays as $holiday)
                <flux:table.row class="hover:bg-accent-content/10">
                    <flux:table.cell>{{ $holiday->id }}</flux:table.cell>
                    <flux:table.cell>{{ $holiday->name }}</flux:table.cell>
                    <flux:table.cell>{{ $holiday->date }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="{{ $holiday->is_recurring ? 'green' : 'red' }}" size="sm" inset="top bottom">
                            {{ $holiday->is_recurring ? __('common.yes') : __('common.no') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $holiday->createdBy->username ?? __('common.inserted_by_null_alt') }}
                    </flux:table.cell>
                    <flux:table.cell>{{ $holiday->created_at->timezone('America/Lima')->format('d/m/Y g:i A') }}
                    </flux:table.cell>
                    <flux:table.cell>{{ $holiday->updatedBy->username ?? __('common.inserted_by_null_alt') }}
                    </flux:table.cell>
                    <flux:table.cell>{{ $holiday->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') }}
                    </flux:table.cell>
                    <flux:table.cell>
                            @canany(['sys.admin', 'holiday.edit', 'holiday.delete'])
                                <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom"
                                             title="{{ __('common.edit') }}"
                                             href="{{ route('holiday.edit', ['holidayId' => $holiday->id]) }}"
                                             wire:navigate>
                                </flux:button>
                            @endcanany
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="9" class="text-center text-lg md:text-xl font-light">
                        {{ __('holiday.errors.empty_set') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    <x-shared.pagination :paginator="$this->holidays"></x-shared.pagination>
</div>
