<?php

use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Clinic;
use App\Models\ClinicMedicine;
use App\Models\Medicine;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination, WithoutUrlPagination;

    public string $searchColumn = 'id';
    public string $keyword = '';
    public string $clinicId = '';
    public string $medicineId = '';

    public string $visibilityFilter = 'visible';
    public bool $searching = false;

    public array $clinics = [];
    public array $medicines = [];

    public function mount(): void
    {
        $this->clinics = Clinic::select(['id', 'name'])->whereNull('deleted_at')->orderBy('name')->get()->toArray();
        $this->medicines = Medicine::select(['id', 'name'])->whereNull('deleted_at')->orderBy('name')->get()->toArray();

        $this->clinicId = (string)($this->clinics[0]['id'] ?? '');
        $this->medicineId = (string)($this->medicines[0]['id'] ?? '');
    }

    protected function rules(): array
    {
        return match ($this->searchColumn) {
            'id' => ['keyword' => ['required', 'regex:/^\d+$/']],
            'clinic_id' => ['clinicId' => ['required', 'exists:clinics,id']],
            'medicine_id' => ['medicineId' => ['required', 'exists:medicines,id']],
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
        $this->reset(['keyword', 'clinicId', 'medicineId', 'searching', 'searchColumn']);
        $this->clinicId = (string)($this->clinics[0]['id'] ?? '');
        $this->medicineId = (string)($this->medicines[0]['id'] ?? '');
        $this->resetValidation();
        $this->resetPage();
    }

    #[Computed]
    public function hasClinics(): bool
    {
        return count($this->clinics) > 0;
    }

    #[Computed]
    public function hasMedicines(): bool
    {
        return count($this->medicines) > 0;
    }

    #[Computed]
    public function clinicMedicines()
    {
        $query = ClinicMedicine::with([
            'clinic:id,name',
            'medicine:id,name,presentation_id',
            'medicine.presentation:id,description',
        ]);

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
                case 'medicine_id':
                    $query->where('medicine_id', $this->medicineId);
                    break;
            }
        }

        return $query->orderBy('updated_at', 'desc')->paginate(12);
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('clinic-medicine.index')])
            ->title(__('views.clinic_medicine.index'));
    }
};
?>

<div>
    <div class="flex flex-col md:flex-row w-full items-stretch md:items-center justify-between gap-4 mb-2">
        @canany(['sys.admin', 'clinicMedicine.create'])
            <flux:button variant="primary" icon="plus" wire:navigate
                         href="{{ route('clinicMedicine.create') }}"
                         class="w-full md:w-auto">
                {{ __('common.new') }}
            </flux:button>
        @endcanany
        <form wire:submit.prevent="search"
              class="flex flex-col md:flex-row items-stretch md:items-center gap-2 w-full md:w-auto md:ml-auto">
            <flux:select wire:model.live="searchColumn" class="w-full md:w-44">
                <flux:select.option value="id">{{ __('common.id') }}</flux:select.option>
                <flux:select.option value="clinic_id">{{ trans_choice('clinic.clinic', 1) }}</flux:select.option>
                <flux:select.option value="medicine_id">{{ trans_choice('medicine.medicine', 1) }}</flux:select.option>
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
                    <flux:input readonly :placeholder="__('clinic-medicine.errors.no_clinics_available')"
                                class="w-full md:w-64"/>
                @endif
                <flux:error name="clinicId"/>
            @elseif ($searchColumn === 'medicine_id')
                @if ($this->hasMedicines)
                    <flux:select wire:model.live="medicineId" class="w-full md:w-64">
                        @foreach ($medicines as $medicine)
                            <flux:select.option
                                value="{{ $medicine['id'] }}">{{ $medicine['name'] }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @else
                    <flux:input readonly :placeholder="__('clinic-medicine.errors.no_medicines_available')"
                                class="w-full md:w-64"/>
                @endif
                <flux:error name="medicineId"/>
            @endif
            @php
                $searchDisabled = ($searchColumn === 'clinic_id'   && !$this->hasClinics)
                               || ($searchColumn === 'medicine_id' && !$this->hasMedicines);
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
            <flux:table.column>{{ trans_choice('medicine.medicine', 1) }}</flux:table.column>
            <flux:table.column>{{ trans_choice('presentation.presentation',1) }}</flux:table.column>
            <flux:table.column>{{ __('common.buy_price_alt') }}</flux:table.column>
            <flux:table.column>{{ __('common.sell_price_alt') }}</flux:table.column>
            <flux:table.column>{{ __('common.stock') }}</flux:table.column>
            <flux:table.column>{{ __('clinic-medicine.salable') }}</flux:table.column>
            <flux:table.column>{{ __('common.status') }}</flux:table.column>
            <flux:table.column>{{ __('common.updated_at') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->clinicMedicines as $cm)
                <flux:table.row class="hover:bg-accent-content/10">
                    <flux:table.cell>{{ $cm->id }}</flux:table.cell>
                    <flux:table.cell>{{ $cm->clinic->name ?? __('common.null') }}</flux:table.cell>
                    <flux:table.cell
                        class="font-medium">{{ $cm->medicine->name ?? __('common.null') }}</flux:table.cell>
                    <flux:table.cell class="text-zinc-500 dark:text-zinc-400 text-sm">
                        {{ $cm->medicine->presentation->description ?? __('common.null') }}
                    </flux:table.cell>
                    <flux:table.cell class="font-mono tabular-nums">
                        {{ number_format($cm->buy_price, 2) }}
                    </flux:table.cell>

                    <flux:table.cell class="font-mono tabular-nums">
                        {{ number_format($cm->sell_price, 2) }}
                    </flux:table.cell>
                    <flux:table.cell class="font-mono tabular-nums">
                        {{ number_format($cm->stock, 0) }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            color="{{ $cm->salable ? 'green' : 'zinc' }}"
                            size="sm"
                            inset="top bottom"
                        >
                            {{ $cm->salable ? __('common.yes') : __('common.no') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            color="{{ $cm->deleted_at ? 'red' : 'green' }}"
                            size="sm"
                            inset="top bottom"
                        >
                            {{ $cm->deleted_at ? __('common.disabled_entity') : __('common.active') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $cm->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        @canany(['sys.admin', 'clinicMedicine.edit', 'clinicMedicine.delete', 'clinicMedicine.restore'])
                            <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom"
                                         title="{{ __('common.edit') }}"
                                         href="{{ route('clinicMedicine.edit', ['clinicMedicineId' => $cm->id]) }}"
                                         wire:navigate/>
                        @endcanany
                        @canany(['sys.admin', 'clinicMedicine.detail'])
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"
                                         title="{{ __('common.details') }}"
                                         href="{{ route('clinicMedicine.detail', ['clinicMedicineId' => $cm->id]) }}"
                                         wire:navigate>
                            </flux:button>
                        @endcanany
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="11" class="text-center text-lg md:text-xl font-light">
                        {{ __('clinic-medicine.errors.empty_set') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <x-shared.pagination :paginator="$this->clinicMedicines"/>

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
