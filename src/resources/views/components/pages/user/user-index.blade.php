<?php

use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\User;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination, WithoutUrlPagination;

    public string $keyword = '';
    public string $searchColumn = 'id';
    public string $visibilityFilter = 'visible';
    public bool $searching = false;

    protected function rules(): array
    {
        $rules = [
            'id' => ['regex:/^\d+$/'],
            'username' => ['required', 'string', 'min:3']
        ];
        return [
            'searchColumn' => ['required', 'in:id,username'],
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
    public function users()
    {
        $query = User::query()->with(['doctor', 'nurse', 'worker']);

        if ($this->visibilityFilter) {
            switch ($this->visibilityFilter) {
                case 'all':
                    $query->withTrashed();
                    break;
                case 'trashed':
                    $query->withTrashed()->where(function ($q) {
                        $q->whereNotNull('deleted_at')
                            ->orWhereNotNull('locked_until');
                    });
                    break;
                case 'visible':
                default:
                    $query->whereNull('locked_until');
                    break;
            }
        }

        if ($this->searching && $this->keyword) {
            switch ($this->searchColumn) {
                case 'id':
                    $query->where('id', $this->keyword);
                    break;
                case 'username':
                    $query->whereLike('username', "%{$this->keyword}%", caseSensitive: false);
                    break;
            }
        }

        return $query->orderBy('updated_at', 'desc')->paginate(12);
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('user.index')])
            ->title(__('views.user.index'));
    }
};
?>

<div>
    <div class="flex flex-col md:flex-row w-full items-stretch md:items-center justify-between gap-4 mb-2">
        <form wire:submit.prevent="search"
              class="flex flex-col md:flex-row items-stretch md:items-center gap-2 w-full md:w-auto md:ml-auto">
            <flux:select wire:model.live="searchColumn" class="w-full md:w-40">
                <flux:select.option value="id">{{ __('common.id') }}</flux:select.option>
                <flux:select.option value="username">{{ __('common.username') }}</flux:select.option>
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
            <flux:table.column>{{ __('common.avatar')  }}</flux:table.column>
            <flux:table.column>{{ __('common.username') }}</flux:table.column>
            <flux:table.column>{{ __('common.owner') }}</flux:table.column>
            <flux:table.column>{{ __('common.position') }}</flux:table.column>
            <flux:table.column>{{ __('common.email') }}</flux:table.column>
            <flux:table.column>{{ __('common.locked_until') }}</flux:table.column>
            <flux:table.column>{{ __('common.status') }}</flux:table.column>
            <flux:table.column>{{ __('common.updated_at') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse($this->users as $user)
                <flux:table.row class="hover:bg-accent-content/10">
                    <flux:table.cell>{{ $user->id }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:avatar size="md" class="max-sm:size-8" name="{{$user->username}}" color="auto"
                                     src="{{$user->avatar ? Storage::url($user->avatar) : null}}"/>
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->username }}</flux:table.cell>
                    <flux:table.cell>{{ $user->worker?->names ?? $user->doctor?->names ?? $user->nurse?->names ?? __('common.null') }}</flux:table.cell>
                    <flux:table.cell>{{ match(true) {
                                            $user->worker !== null => ucfirst(mb_strtolower($user->worker->position)) ?? trans_choice('worker.worker',1),
                                            $user->doctor !== null => trans_choice('doctor.doctor',1),
                                            $user->nurse !== null  => trans_choice('nurse.nurse',1),
                                            default                => __('common.null')}
                                     }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>{{ $user->locked_until != null ? $user->locked_until->timezone('America/Lima')->format('d/m/Y g:i A') : __('common.null') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="{{ $user->deleted_at || $user->locked_until != null ? 'red' : 'green' }}"
                                    size="sm" inset="top bottom">
                            {{ $user->deleted_at ? __('common.disabled_entity') : ($user->locked_until !== null ? __('common.locked_account') : __('common.active')) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button.group>
                            @canany(['sys.admin', 'user.edit'])
                                <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom"
                                             title="{{ __('common.edit') }}"
                                             href="{{route('user.edit',['userId' => $user->id])}}"
                                             wire:navigate>
                                </flux:button>
                            @endcanany
                            @canany(['sys.admin', 'user.detail','user.delete', 'user.restore'])
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"
                                             title="{{ __('common.details') }}"
                                             href="{{route('user.detail', ['userId' => $user->id])}}"
                                             wire:navigate>
                                </flux:button>
                            @endcanany
                        </flux:button.group>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center text-lg md:text-xl font-light">
                        {{ __('user.errors.empty_set') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    <x-shared.pagination :paginator="$this->users"></x-shared.pagination>
    <div class="flex flex-col items-end mt-2">
        <flux:select wire:model.live="visibilityFilter" wire:loading.attr="disabled" class="w-full md:w-50">
            <flux:select.option value="all">{{ __('common.index_filter.all') }}</flux:select.option>
            <flux:select.option value="visible">{{ __('common.index_filter.only_visible') }}</flux:select.option>
            <flux:select.option value="trashed">{{ __('common.index_filter.trashed') }}</flux:select.option>
        </flux:select>
    </div>
</div>
