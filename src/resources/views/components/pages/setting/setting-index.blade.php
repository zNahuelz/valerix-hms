<?php

use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Setting;
use App\Enums\SettingType;
use Livewire\Attributes\Computed;
use Illuminate\Validation\Rule;

new class extends Component {
    use WithPagination, WithoutUrlPagination;

    public string $searchColumn = 'id';
    public string $keyword = '';
    public string $valueType = '';
    public bool $searching = false;

    public function mount(): void
    {
        $this->valueType = SettingType::cases()[0]->value;
    }

    protected function rules(): array
    {
        return match ($this->searchColumn) {
            'id' => ['keyword' => ['required', 'regex:/^\d+$/']],
            'key' => ['keyword' => ['required', 'string', 'min:1']],
            'value_type' => ['valueType' => ['required', Rule::enum(SettingType::class)]],
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

    public function search(): void
    {
        $this->keyword = trim($this->keyword);
        $this->validate();
        $this->searching = true;
        $this->resetPage();
    }

    public function resetSearch(): void
    {
        $this->reset(['keyword', 'searching', 'searchColumn']);
        $this->valueType = SettingType::cases()[0]->value;
        $this->resetValidation();
        $this->resetPage();
    }

    #[Computed]
    public function settings()
    {
        $query = Setting::query();

        if ($this->searching) {
            switch ($this->searchColumn) {
                case 'id':
                    $query->where('id', $this->keyword);
                    break;
                case 'key':
                    $query->whereLike('_key', "%{$this->keyword}%", caseSensitive: false);
                    break;
                case 'value_type':
                    $query->where('value_type', $this->valueType);
                    break;
            }
        }

        return $query->orderBy('updated_at', 'desc')->paginate(12);
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('setting.index')])
            ->title(__('views.setting.index'));
    }
};
?>

<div>
    <div class="flex flex-col md:flex-row w-full items-stretch md:items-center justify-between gap-4 mb-2">
        @canany(['sys.admin', 'setting.create'])
            <flux:button variant="primary" icon="plus" wire:navigate
                         href="{{ route('setting.create') }}"
                         class="w-full md:w-auto">
                {{ __('common.new') }}
            </flux:button>
        @endcanany
        <form wire:submit.prevent="search"
              class="flex flex-col md:flex-row items-stretch md:items-center gap-2 w-full md:w-auto md:ml-auto">
            <flux:select wire:model.live="searchColumn" class="w-full md:w-44">
                <flux:select.option value="id">{{ __('common.id') }}</flux:select.option>
                <flux:select.option value="key">{{ __('common.key') }}</flux:select.option>
                <flux:select.option value="value_type">{{ __('common.value_type') }}</flux:select.option>
            </flux:select>

            @if ($searchColumn === 'value_type')
                <flux:select wire:model.live="valueType" class="w-full md:w-64">
                    @foreach(SettingType::cases() as $type)
                        <flux:select.option value="{{ $type->value }}">{{ $type->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="valueType"/>
            @else
                <flux:input
                    wire:model="keyword"
                    type="{{ $searchColumn === 'id' ? 'number' : 'text' }}"
                    min="{{ $searchColumn === 'id' ? '1' : null }}"
                    :placeholder="__('common.search') . '...'"
                    class="w-full md:w-64"
                />
                <flux:error name="keyword"/>
            @endif
            <flux:button.group>
                <flux:button type="button" icon="arrow-path" square
                             wire:click="resetSearch"
                             wire:loading.attr="disabled"
                             wire:target="resetSearch"
                             class="w-full md:w-auto p-3"/>
                <flux:button type="submit" icon="magnifying-glass"
                             wire:loading.attr="disabled"
                             wire:target="search"
                             class="w-full md:w-auto">
                    {{ __('common.search') }}
                </flux:button>
            </flux:button.group>
        </form>
    </div>
    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('common.id_alt') }}</flux:table.column>
            <flux:table.column>{{ __('common.key') }}</flux:table.column>
            <flux:table.column>{{ __('common.value') }}</flux:table.column>
            <flux:table.column>{{ __('common.value_type') }}</flux:table.column>
            <flux:table.column>{{ __('common.updated_at') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->settings as $setting)
                <flux:table.row class="hover:bg-accent-content/10">
                    <flux:table.cell>{{ $setting->id }}</flux:table.cell>
                    <flux:table.cell class="font-mono text-sm">{{ $setting->_key }}</flux:table.cell>
                    <flux:table.cell class="font-mono tabular-nums text-sm max-w-xs truncate">
                        @if(($setting->value_type instanceof SettingType ? $setting->value_type->value : $setting->value_type) === SettingType::BOOLEAN->value)
                            <flux:badge color="{{ $setting->value ? 'green' : 'zinc' }}" size="sm" inset="top bottom">
                                {{ $setting->value ? __('common.true') : __('common.false') }}
                            </flux:badge>
                        @else
                            {{ $setting->value }}
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @php
                            $type = $setting->value_type instanceof SettingType
                                ? $setting->value_type
                                : SettingType::from($setting->value_type);
                        @endphp
                        <flux:badge color="blue" size="sm" inset="top bottom">
                            {{ $type->label() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $setting->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        @canany(['sys.admin', 'setting.edit', 'setting.delete'])
                            <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom"
                                         title="{{ __('common.edit') }}"
                                         href="{{ route('setting.edit', ['settingId' => $setting->id]) }}"
                                         wire:navigate/>
                        @endcanany
                        @canany(['sys.admin', 'setting.detail'])
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"
                                         title="{{ __('common.details') }}"
                                         href="{{ route('setting.detail', ['settingId' => $setting->id]) }}"
                                         wire:navigate/>
                        @endcanany
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center text-lg md:text-xl font-light">
                        {{ __('setting.errors.empty_set') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    <x-shared.pagination :paginator="$this->settings"/>
</div>
