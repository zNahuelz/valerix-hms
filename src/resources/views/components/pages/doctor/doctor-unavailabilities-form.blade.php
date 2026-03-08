<?php

use Livewire\Component;
use App\Models\Doctor;
use App\Models\DoctorUnavailability;
use App\Livewire\Forms\Doctor\DoctorUnavailabilitiesForm;
use Illuminate\Support\Facades\Session;
use App\Enums\UnavailabilityReason;

new class extends Component {
    public DoctorUnavailabilitiesForm $form;
    public array $doctors = [];

    public function mount(?string $unavId = null): void
    {
        if (!Doctor::whereNull('deleted_at')->exists()) {
            Session::flash('error', __('doctors.errors.unav_creation_disabled_empty_doctors'));
            $this->redirectRoute('doctor.index');
            return;
        }

        $this->doctors = Doctor::select(['id', 'names', 'paternal_surname', 'dni'])
            ->whereNull('deleted_at')
            ->orderBy('names')
            ->get()
            ->toArray();

        if (!$unavId) {
            $this->form->doctor_id = $this->doctors[0]['id'];
            $this->form->reason = UnavailabilityReason::cases()[0]->value;
            return;
        }

        if (!is_numeric($unavId)) {
            $this->redirectWithError($unavId);
            return;
        }

        $unav = DoctorUnavailability::withTrashed()->find((int)$unavId);

        if (!$unav) {
            $this->redirectWithError($unavId);
            return;
        }

        $unav->load(['doctor']);

        $this->form->doctorUnavailability = $unav;
        $this->form->doctor_id = $unav->doctor_id;
        $this->form->reason = $unav->getRawOriginal('reason');
        $this->form->fill($unav->toArray());
    }

    protected function redirectWithError($unavId)
    {
        Session::flash('error', __('doctor.unavailability.not_found', ['id' => $unavId]));
        $this->redirectRoute('doctor.index');
    }

    public function save()
    {
        $this->validate();
        if ($this->form->doctorUnavailability) {
            $this->form->doctorUnavailability->update([
                'doctor_id' => $this->form->doctor_id,
                'start_datetime' => $this->form->start_datetime,
                'end_datetime' => $this->form->end_datetime,
                'reason' => $this->form->reason,
            ]);
            Session::flash('success', __('doctor.unavailability.updated', ['id' => $this->form->doctorUnavailability->id]));
        } else {
            $unav = DoctorUnavailability::create([
                'doctor_id' => $this->form->doctor_id,
                'start_datetime' => $this->form->start_datetime,
                'end_datetime' => $this->form->end_datetime,
                'reason' => $this->form->reason,
            ]);
            Session::flash('success', __('doctor.unavailability.created', ['id' => $unav->id]));
        }
        return redirect()->to(route('doctor.detail.unavailabilities', ['doctorId' => $this->form->doctorUnavailability->doctor_id]));
    }

    public function delete()
    {
        if ($this->form->doctorUnavailability) {
            if ($this->form->doctorUnavailability->trashed()) {
                $this->form->doctorUnavailability->restore();
                Session::flash('success', __('doctor.unavailability.restored', ['id' => $this->form->doctorUnavailability->id]));
            } else {
                $this->form->doctorUnavailability->delete();
                Session::flash('success', __('doctor.unavailability.deleted', ['id' => $this->form->doctorUnavailability->id]));
            }
        }
        return redirect()->to(route('doctor.detail.unavailabilities', ['doctorId' => $this->form->doctorUnavailability->doctor_id]));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->doctorUnavailability ? 'doctor.unavailability.edit' : 'doctor.unavailability.create')])
            ->title(__($this->form->doctorUnavailability ? 'views.doctor.edit_unavailability' : 'views.doctor.create_unavailability'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-3xl" wire:submit="save">
        @if($this->form->doctorUnavailability && $this->form->doctorUnavailability->trashed())
            <x-shared.alert type="info">{{ __('doctor.unavailability.is_deleted') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-3" wire:loading.attr="disabled"
                       wire:target="save, delete">
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ trans_choice('doctor.doctor',1) }}</flux:label>
                <flux:select wire:model.live.blur="form.doctor_id">
                    @foreach($this->doctors as $doctor)
                        <flux:select.option
                            value="{{$doctor['id']}}">{{$doctor['names'] .' '. $doctor['paternal_surname']  .' - '. $doctor['dni']}}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="form.doctor_id"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.start_datetime') }}</flux:label>
                <flux:input wire:model.live.blur="form.start_datetime" type="datetime-local"/>
                <flux:error name="form.start_datetime"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.end_datetime') }}</flux:label>
                <flux:input wire:model.live.blur="form.end_datetime" type="datetime-local"/>
                <flux:error name="form.end_datetime"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.reason') }}</flux:label>
                <flux:select wire:model.live.blur="form.reason">
                    @foreach(UnavailabilityReason::cases() as $reason)
                        <flux:select.option
                            value="{{$reason->value}}">{{$reason->label()}}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="form.reason"/>
            </flux:field>

            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @if($this->form->doctorUnavailability)
                        @canany(['sys.admin', 'doctorUnavailability.delete', 'doctorUnavailability.restore'])
                            <flux:button type="button" variant="primary"
                                         color="{{ $this->form->doctorUnavailability->trashed() ? 'amber' : 'red' }}"
                                         wire:click="delete"
                                         class="w-full md:w-auto" wire:loading.attr="disabled"
                                         wire:target="delete, save">
                                {{ $this->form->doctorUnavailability->trashed() ? __('common.restore') : __('common.delete') }}
                            </flux:button>
                        @endcanany
                    @endif
                    @canany(['sys.admin', 'doctorUnavailability.create', 'doctorUnavailability.update'])
                        <flux:button type="submit" variant="primary" class="w-full md:w-auto md:ml-auto"
                                     wire:loading.attr="disabled" wire:target="delete, save">
                            {{ $this->form->doctorUnavailability ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
