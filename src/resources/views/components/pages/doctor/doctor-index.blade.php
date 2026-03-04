<?php

use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Doctor;
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
            'dni' => ['required','regex:/^[0-9]{8,15}$/'],
            'names' => ['required','string', 'min:3'],
            'paternal_surname' => ['required','string', 'min:3'],
        ];
        return [
            'searchColumn' => ['required', 'in:id,names,dni,paternal_surname'],
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
    public function doctors()
    {
        $query = Doctor::query();

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
                case 'names':
                    $query->whereLike('names', "%{$this->keyword}%", caseSensitive: false);
                    break;
                case 'paternal_surname':
                    $query->whereLike('paternal_surname', "%{$this->keyword}%", caseSensitive: false);
                    break;
                case 'dni':
                    $query->where('dni', $this->keyword);
                    break;
            }
        }

        return $query->orderBy('updated_at', 'desc')->paginate(12);
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('doctor.index')])
            ->title(__('views.doctor.index'));
    }
};
?>

<div>
    <div class="flex flex-col md:flex-row w-full items-stretch md:items-center justify-between gap-4 mb-2">
        @canany(['sys.admin', 'doctor.create'])
            <flux:button variant="primary" icon="plus" wire:navigate href="{{ route('doctor.create') }}"
                         class="w-full md:w-auto">
                {{ __('common.new') }}
            </flux:button>
        @endcanany
        <form wire:submit.prevent="search"
              class="flex flex-col md:flex-row items-stretch md:items-center gap-2 w-full md:w-auto md:ml-auto">
            <flux:select wire:model.live="searchColumn" class="w-full md:w-40">
                <flux:select.option value="id">{{ __('common.id') }}</flux:select.option>
                <flux:select.option value="names">{{ trans_choice('common.name', 1) }}</flux:select.option>
                <flux:select.option value="paternal_surname">{{ __('common.paternal_surname') }}</flux:select.option>
                <flux:select.option value="dni">{{ __('common.dni') }}</flux:select.option>
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
            <flux:table.column>{{ trans_choice('common.name', 2) }}</flux:table.column>
            <flux:table.column>{{ __('common.paternal_surname') }}</flux:table.column>
            <flux:table.column>{{ __('common.dni') }}</flux:table.column>
            <flux:table.column>{{ __('common.phone')  }}</flux:table.column>
            <flux:table.column>{{ __('common.status') }}</flux:table.column>
            <flux:table.column>{{ __('common.updated_at') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse($this->doctors as $doctor)
                <flux:table.row class="hover:bg-accent-content/10">
                    <flux:table.cell>{{ $doctor->id }}</flux:table.cell>
                    <flux:table.cell>{{ $doctor->names }}</flux:table.cell>
                    <flux:table.cell>{{ $doctor->paternal_surname }}</flux:table.cell>
                    <flux:table.cell>{{ $doctor->dni }}</flux:table.cell>
                    <flux:table.cell>{{ $doctor->phone ?? __('common.null') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="{{ $doctor->deleted_at ? 'red' : 'green' }}" size="sm" inset="top bottom">
                            {{ $doctor->deleted_at ? __('common.disabled_entity') : __('common.enabled_entity') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $doctor->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button.group>
                            @if(auth()->id() !== $doctor->user_id)
                                @canany(['sys.admin', 'doctor.edit', 'doctor.delete', 'doctor.restore'])
                                    <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom"
                                                 title="{{ __('common.edit') }}"
                                                 href="{{route('doctor.edit', ['doctorId' => $doctor->id])}}"
                                                 wire:navigate>
                                    </flux:button>
                                @endcanany
                                @canany(['sys.admin', 'doctor.edit.availabilities'])
                                        <flux:button variant="ghost" size="sm" icon="calendar-days" inset="top bottom"
                                                     title="{{ __('common.edit_availabilities') }}"
                                                     href="{{route('doctor.edit.availabilities', ['doctorId' => $doctor->id])}}"
                                                     wire:navigate>
                                        </flux:button>
                                @endcanany
                                    @canany(['sys.admin', 'doctor.detail.unavailabilities'])
                                        <flux:button variant="ghost" size="sm" icon="clipboard-document-check" inset="top bottom"
                                                     title="{{ __('common.unavailabilities_detail') }}"
                                                     href="{{route('doctor.detail.unavailabilities', ['doctorId' => $doctor->id])}}"
                                                     wire:navigate>
                                        </flux:button>
                                    @endcanany
                                @canany(['sys.admin', 'doctor.detail'])
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"
                                                 title="{{ __('common.details') }}"
                                                 href="{{ route('doctor.detail', ['doctorId' => $doctor->id]) }}"
                                                 wire:navigate>
                                    </flux:button>
                                @endcanany
                            @endif
                        </flux:button.group>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center text-lg md:text-xl font-light">
                        {{ __('doctor.errors.empty_set') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    <x-shared.pagination :paginator="$this->doctors"></x-shared.pagination>
    <div class="flex flex-col items-end mt-2">
        <flux:select wire:model.live="visibilityFilter" wire:loading.attr="disabled" class="w-full md:w-50">
            <flux:select.option value="all">{{ __('common.index_filter.all') }}</flux:select.option>
            <flux:select.option value="visible">{{ __('common.index_filter.only_visible') }}</flux:select.option>
            <flux:select.option value="trashed">{{ __('common.index_filter.trashed') }}</flux:select.option>
        </flux:select>
    </div>
</div>
