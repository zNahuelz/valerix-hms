<?php

use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Doctor;
use App\Models\DoctorUnavailability;
use Illuminate\Support\Carbon;


new class extends Component {
    use WithPagination, WithoutUrlPagination;

    public ?Doctor $doctor = null;

    public function mount(?string $doctorId = null): void
    {
        if ($doctorId) {
            if (!is_numeric($doctorId)) {
                $this->redirectWithError($doctorId);
                return;
            }

            $doctor = Doctor::withTrashed()->find((int)$doctorId);

            if (!$doctor) {
                $this->redirectWithError($doctorId);
                return;
            }

            $doctor->load([
                'clinic',
            ]);

            $this->doctor = $doctor;
        }
    }

    protected function redirectWithError($doctorId)
    {
        Session::flash('error', __('doctor.errors.not_found', ['id' => $doctorId]));
        $this->redirectRoute('doctor.index');
    }

    public function getUnavailabilitiesProperty()
    {
        if (!$this->doctor) return collect();
        return DoctorUnavailability::where('doctor_id', $this->doctor->id)
            ->orderBy('start_datetime', 'desc')
            ->paginate(10);
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('doctor.unavailability.index', ['id' => $this->doctor->id, 'name' => ucwords(strtolower($this->doctor->names))
                . ' ' .
                ucwords(strtolower($this->doctor->paternal_surname))])])
            ->title(__('views.doctor.unavailabilities'));
    }
};
?>

<div>
    <div class="flex flex-col md:flex-row w-full items-stretch md:items-center justify-between gap-4 mb-2">
        @canany(['sys.admin', 'doctor.create.unavailability'])
            <flux:button variant="primary" icon="plus" wire:navigate
                         href="{{ route('doctor.create.unavailabilities') }}"
                         class="w-full md:w-auto">
                {{ __('common.new') }}
            </flux:button>
        @endcanany
    </div>
    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('common.id_alt') }}</flux:table.column>
            <flux:table.column>{{ __('common.dni') }}</flux:table.column>
            <flux:table.column>{{ __('common.start_datetime') }}</flux:table.column>
            <flux:table.column>{{ __('common.end_datetime') }}</flux:table.column>
            <flux:table.column>{{ __('common.reason') }}</flux:table.column>
            <flux:table.column>{{ __('common.status') }}</flux:table.column>
            <flux:table.column>{{ __('common.created_at') }}</flux:table.column>
            <flux:table.column>{{ __('common.updated_at') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse($this->unavailabilities as $unav)
                <flux:table.row class="hover:bg-accent-content/10">
                    <flux:table.cell>{{ $unav->id }}</flux:table.cell>
                    <flux:table.cell>{{ $this->doctor->dni }}</flux:table.cell>
                    <flux:table.cell>{{ Carbon::parse($unav->start_datetime)->timezone('America/Lima')->format('d/m/Y g:i A') }}</flux:table.cell>
                    <flux:table.cell>{{ Carbon::parse($unav->end_datetime)->timezone('America/Lima')->format('d/m/Y g:i A') }}</flux:table.cell>
                    <flux:table.cell>{{$unav->reason->label() }}</flux:table.cell>
                    <flux:table.cell>
                        @php
                            $now = Carbon::now('America/Lima');
                            $start = Carbon::parse($unav->start_datetime)->timezone('America/Lima');
                            $end = Carbon::parse($unav->end_datetime)->timezone('America/Lima');
                            $isActive = $now->between($start, $end);
                        @endphp
                        <flux:badge color="{{ $isActive ? 'green' : 'zinc' }}" size="sm" inset="top bottom">
                            {{ $isActive ? __('common.active') : __('common.inactive') }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>{{ $unav->created_at->timezone('America/Lima')->format('d/m/Y g:i A') }}</flux:table.cell>
                    <flux:table.cell>{{ $unav->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button.group>
                            @if(auth()->id() !== $this->doctor->user_id)
                                @canany(['sys.admin', 'doctor.unavailability.edit', 'doctor.unavailability.delete', 'doctor.unavailability.restore'])
                                    <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom"
                                                 title="{{ __('common.edit') }}"
                                                 href="{{ route('doctor.edit.unavailabilities', ['unavId' => $unav->id]) }}"
                                                 wire:navigate>
                                    </flux:button>
                                @endcanany
                                @canany(['sys.admin', 'doctor.detail'])
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"
                                                 title="{{ __('common.details') }}"
                                                 href="{{ route('doctor.detail', ['doctorId' => $this->doctor->id]) }}"
                                                 wire:navigate>
                                    </flux:button>
                                @endcanany
                            @endif
                        </flux:button.group>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="9" class="text-center text-lg md:text-xl font-light">
                        {{ __('doctor.unavailability.empty_set') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    <x-shared.pagination :paginator="$this->unavailabilities"></x-shared.pagination>
</div>
