<?php

use Livewire\Component;
use App\Models\Doctor;
use Illuminate\Support\Carbon;

new class extends Component {
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
                'user' => fn($q) => $q->withTrashed()->with('roles'),
                'updatedBy',
                'availabilities' => fn($q) => $q->orderBy('weekday', 'asc'),
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

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('doctor.detail', ['id' => $this->doctor->id, 'name' => ucwords(strtolower($this->doctor->names))
                . ' ' .
                ucwords(strtolower($this->doctor->paternal_surname))])])
            ->title(__('views.doctor.detail'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full">
        @if($doctor && $doctor->trashed())
            <x-shared.alert type="info">{{ __('doctor.is_deleted_alt') }}</x-shared.alert>
        @endif

        @if($doctor && $doctor->availabilities->isEmpty())
            <x-shared.alert type="error">{{ __('doctor.errors.empty_availabilities') }}</x-shared.alert>
        @endif

        <div @class([
    'grid grid-cols-1 md:grid-cols-2 gap-12' => $doctor->availabilities->isNotEmpty(),
    'flex justify-center' => $doctor->availabilities->isEmpty(),
        ])>
            <flux:fieldset @class([
    'grid grid-cols-1 md:grid-cols-2 gap-2',
    'w-full md:max-w-3xl' => $doctor->availabilities->isEmpty()])>
                <div class="col-span-full">
                    <flux:legend class="text-xl! mb-2">{{ __('common.personal_info') }}</flux:legend>
                    <flux:separator></flux:separator>
                </div>
                <flux:field>
                    <flux:label>{{ __('common.dni') }}</flux:label>
                    <flux:input readonly value="{{ $doctor->dni }}" type="text"/>
                </flux:field>
                <flux:field>
                    <flux:label>{{ trans_choice('common.name', 1) }}</flux:label>
                    <flux:input readonly value="{{ $doctor->names }}" type="text"/>
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('common.paternal_surname') }}</flux:label>
                    <flux:input readonly value="{{ $doctor->paternal_surname }}" type="text"/>
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('common.maternal_surname') }}</flux:label>
                    <flux:input readonly value="{{ $doctor->maternal_surname ?? __('common.null') }}" type="text"/>
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('common.hired_at') }}</flux:label>
                    <flux:input readonly
                                value="{{ Carbon::createFromFormat('Y-m-d',$doctor->hired_at)->timezone('America/Lima')->format('d/m/Y') ?? __('common.null') }}"
                                type="text"/>
                </flux:field>
                <flux:input readonly value="{{ $doctor->user?->email ?? __('common.null') }}"
                            label="{{ __('common.email') }}" type="email"/>
                <flux:input readonly value="{{ $doctor->phone ?? __('common.null') }}" label="{{ __('common.phone') }}"
                            type="text"/>
                <flux:input readonly value="{{ $doctor->address ?? __('common.null') }}"
                            label="{{ __('common.address') }}"
                            type="text"/>
                <flux:field>
                    <flux:label>{{ trans_choice('auth.user',1) }}</flux:label>
                    <flux:input.group>
                        <flux:input readonly value="{{ $doctor->user?->username ?? __('common.null') }}"
                                    type="text"/>
                        <flux:button type="button" variant="primary" color="cyan"
                                     icon="ellipsis-horizontal" wire:navigate
                                     href="#">
                        </flux:button>
                    </flux:input.group>
                </flux:field>
                <flux:field>
                    <flux:label>{{ trans_choice('role.role',1) }}</flux:label>
                    <flux:input.group>
                        <flux:input readonly value="{{ $doctor->user?->roles->first()?->name ?? __('common.null') }}"
                                    type="text"/>
                        <flux:button type="button" variant="primary" color="cyan"
                                     icon="ellipsis-horizontal" wire:navigate
                                     href="#">
                        </flux:button>
                    </flux:input.group>
                </flux:field>
                <div class="col-span-full">
                    <flux:input readonly value="{{ $doctor->updatedBy->username ?? __('common.inserted_by_null') }}"
                                label="{{ __('common.updated_by') }}" type="text"/>
                </div>
                <flux:input readonly
                            value="{{ $doctor->created_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                            label="{{ __('common.created_at') }}" type="text"/>
                <flux:input readonly
                            value="{{ $doctor->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                            label="{{ __('common.updated_at_alt') }}" type="text"/>
                <div class="col-span-full">
                    <flux:field>
                        <flux:label>{{ trans_choice('clinic.clinic',1) }}</flux:label>
                        <flux:input.group>
                            <flux:input readonly value="{{ $doctor->clinic?->name }}"
                                        type="text"/>
                            <flux:button type="button" variant="primary" color="cyan"
                                         icon="ellipsis-horizontal" wire:navigate
                                         href="{{route('clinic.detail',['clinicId' => $doctor->clinic?->id])}}">
                            </flux:button>
                        </flux:input.group>
                    </flux:field>
                </div>
                <div class="col-span-full">
                    <div class="flex flex-col md:flex-row md:justify-between gap-2">
                        @if(!auth()->user()->is($doctor->user))
                            <flux:button.group>
                                @canany(['sys.admin', 'doctor.edit.availabilities', 'doctor.detail.unavailabilities'])
                                    <flux:button type="button"
                                                 wire:navigate
                                                 href="{{route('doctor.edit.availabilities', ['doctorId' => $doctor->id])}}"
                                                 class="w-full md:w-auto">
                                        {{  __('common.edit_availabilities') }}
                                    </flux:button>
                                @endcanany
                                @canany(['sys.admin', 'doctor.detail.unavailabilities'])
                                    <flux:button type="button"
                                                 wire:navigate
                                                 href="{{route('doctor.detail.unavailabilities', ['doctorId' => $doctor->id])}}"
                                                 class="w-full md:w-auto">
                                        {{  trans_choice('common.unavailability',2) }}
                                    </flux:button>
                                @endcanany
                            </flux:button.group>

                            @canany(['sys.admin', 'doctor.update', 'doctor.delete', 'doctor.restore'])
                                <flux:button type="button" variant="primary" class="w-full md:w-auto md:ml-auto"
                                             wire:navigate
                                             href="{{ route('doctor.edit', ['doctorId' => $doctor->id]) }}">
                                    {{ __('common.edit') }}
                                </flux:button>
                            @endcanany
                        @endif
                    </div>
                </div>
            </flux:fieldset>
            @if($doctor->availabilities->isNotEmpty())
                <div>
                    <flux:legend class="text-xl! mb-2">{{ __('common.schedule_settings') }}</flux:legend>
                    <flux:separator></flux:separator>
                    <div class="space-y-4 mt-2">
                        @foreach($doctor->availabilities as $av)
                            <flux:card class="space-y-4 gap-2">
                                <div class="flex items-center justify-between">
                                    <flux:heading class="flex items-center gap-2">
                                        {{ __('common.weekdays')[$av->weekday] }}
                                    </flux:heading>
                                    @if(!$av->is_active)
                                        <flux:badge size="sm" color="red">{{ __('common.inactive') }}</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="emerald">{{ __('common.active') }}</flux:badge>
                                    @endif
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                                    <flux:input type="time" readonly label="{{ __('common.start_time') }}"
                                                value="{{ $av->start_time ? Carbon::parse($av->start_time)->format('H:i') : __('common.null') }}"/>
                                    <flux:input type="time" readonly label="{{ __('common.end_time') }}"
                                                value="{{ $av->end_time ?  Carbon::parse($av->end_time)->format('H:i') : __('common.null') }}"/>
                                    <flux:input type="time" readonly label="{{ __('common.break_start') }}"
                                                value="{{ $av->break_start ? Carbon::parse($av->break_start)->format('H:i') : __('common.null') }}"/>
                                    <flux:input type="time" readonly label="{{ __('common.break_end') }}"
                                                value="{{ $av->break_end ? Carbon::parse($av->break_end)->format('H:i') : __('common.null') }}"/>
                                </div>
                            </flux:card>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
