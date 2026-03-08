<?php

use Livewire\Component;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Livewire\Forms\Doctor\DoctorAvailabilitiesForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

new class extends Component {
    public DoctorAvailabilitiesForm $form;
    public ?Doctor $doctor = null;


    public function mount(?string $doctorId = null): void
    {
        if (!$doctorId || !is_numeric($doctorId)) {
            $this->redirectWithError($doctorId ?? '?');
            return;
        }

        $doctor = Doctor::withTrashed()->find((int)$doctorId);

        if (!$doctor) {
            $this->redirectWithError($doctorId);
            return;
        }

        $doctor->load(['availabilities' => fn($q) => $q->orderBy('weekday')]);
        $this->doctor = $doctor;

        if ($doctor->availabilities->isNotEmpty()) {
            $this->form->availabilities = $doctor->availabilities
                ->map(fn($av) => [
                    'id' => $av->id,
                    'weekday' => $av->weekday,
                    'start_time' => $av->start_time ? Carbon::parse($av->start_time)->format('H:i') : '08:00',
                    'end_time' => $av->end_time ? Carbon::parse($av->end_time)->format('H:i') : '17:00',
                    'break_start' => $av->break_start ? Carbon::parse($av->break_start)->format('H:i') : '12:00',
                    'break_end' => $av->break_end ? Carbon::parse($av->break_end)->format('H:i') : '13:00',
                    'is_active' => (bool)$av->is_active,
                ])
                ->toArray();
        } else {
            $this->form->availabilities = collect(range(1, 5))->map(fn($day) => [
                'id' => null,
                'weekday' => $day,
                'start_time' => '08:00',
                'end_time' => '17:00',
                'break_start' => '12:00',
                'break_end' => '13:00',
                'is_active' => true,
            ])->toArray();
        }
    }

    protected function redirectWithError(string $doctorId): void
    {
        Session::flash('error', __('doctor.errors.not_found', ['id' => $doctorId]));
        $this->redirectRoute('doctor.index');
    }

    public function addAvailability(): void
    {
        if (count($this->form->availabilities) >= 7) {
            return;
        }

        $usedWeekdays = array_column($this->form->availabilities, 'weekday');
        $nextWeekday = collect(range(1, 7))->first(fn($d) => !in_array($d, $usedWeekdays));

        if ($nextWeekday) {
            $this->form->availabilities[] = [
                'id' => null,
                'weekday' => $nextWeekday,
                'start_time' => '08:00',
                'end_time' => '17:00',
                'break_start' => '12:00',
                'break_end' => '13:00',
                'is_active' => true,
            ];
        }
    }

    public function save()
    {
        $this->form->validateAndCheck();

        try {
            DB::beginTransaction();

            $sanitized = $this->form->sanitized();
            $incomingIds = collect($sanitized['availabilities'])->pluck('id')->filter()->all();

            // Delete availabilities removed by the user
            $this->doctor->availabilities()
                ->whereNotIn('id', $incomingIds)
                ->delete();

            foreach ($sanitized['availabilities'] as $av) {
                DoctorAvailability::updateOrCreate(
                    ['id' => $av['id'] ?? null, 'doctor_id' => $this->doctor->id],
                    [
                        'doctor_id' => $this->doctor->id,
                        'weekday' => $av['weekday'],
                        'start_time' => $av['start_time'],
                        'end_time' => $av['end_time'],
                        'break_start' => $av['break_start'],
                        'break_end' => $av['break_end'],
                        'is_active' => $av['is_active'],
                    ]
                );
            }

            DB::commit();
            Session::flash('success', __('doctor.updated_availabilities', ['id' => $this->doctor->id]));
            return redirect()->to(route('doctor.detail', ['doctorId' => $this->doctor->id]));
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', __('doctor.errors.availabilities_update_failed'));
            return redirect()->to(route('doctor.index'));
        }
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('doctor.edit_availabilities')])
            ->title(__('views.doctor.edit_availabilities'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-3xl">
        <x-shared.alert>{{__('doctor.edit_availability', ['doctor' => ucwords(strtolower($doctor->names .' '. $doctor->paternal_surname)),'dni' => $doctor->dni, 'id' => $doctor->id ])}}</x-shared.alert>
        <flux:fieldset class="space-y-4">
            <div class="flex items-center justify-between">
                <flux:legend>{{ __('common.schedule_settings') }}</flux:legend>
                @if(count($form->availabilities) < 7)
                    <flux:button type="button" size="sm" wire:click="addAvailability">
                        + {{ __('common.add_day') }}
                    </flux:button>
                @endif
            </div>
            <flux:error name="form.availabilities"/>
            @foreach($form->availabilities as $index => $slot)
                <flux:card class="space-y-4">
                    <div class="flex items-center justify-between">
                        <flux:heading>
                            {{ __('common.weekdays')[$slot['weekday']] }}
                        </flux:heading>
                        <div class="flex items-center gap-3">
                            <flux:checkbox
                                wire:model="form.availabilities.{{ $index }}.is_active"
                                :label="__('common.active')"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                        <flux:field>
                            <flux:label>{{ __('common.start_time') }}</flux:label>
                            <flux:input type="time" wire:model="form.availabilities.{{ $index }}.start_time"/>
                            <flux:error name="form.availabilities.{{ $index }}.start_time"/>
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('common.end_time') }}</flux:label>
                            <flux:input type="time" wire:model="form.availabilities.{{ $index }}.end_time"/>
                            <flux:error name="form.availabilities.{{ $index }}.end_time"/>
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('common.break_start') }}</flux:label>
                            <flux:input type="time" wire:model="form.availabilities.{{ $index }}.break_start"/>
                            <flux:error name="form.availabilities.{{ $index }}.break_start"/>
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('common.break_end') }}</flux:label>
                            <flux:input type="time" wire:model="form.availabilities.{{ $index }}.break_end"/>
                            <flux:error name="form.availabilities.{{ $index }}.break_end"/>
                        </flux:field>
                    </div>
                </flux:card>
            @endforeach
            <div class="flex flex-col md:flex-row md:justify-between gap-2 pt-2">
                <flux:button type="button" wire:navigate
                             href="{{ route('doctor.detail', ['doctorId' => $this->doctor?->id]) }}">
                    {{ __('common.details') }}
                </flux:button>
                @canany(['sys.admin', 'doctor.update'])
                    <flux:button type="button" variant="primary" wire:click="save">
                        {{ __('common.save') }}
                    </flux:button>
                @endcanany
            </div>
        </flux:fieldset>

    </div>
</div>
