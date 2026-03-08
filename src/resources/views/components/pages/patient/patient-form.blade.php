<?php

use Livewire\Component;
use App\Models\Patient;
use App\Livewire\Forms\PatientForm;
use Illuminate\Support\Facades\Session;

new class extends Component {
    public PatientForm $form;

    public function mount(?string $patientId = null): void
    {
        if ($patientId) {
            if (!is_numeric($patientId)) {
                $this->redirectWithError($patientId);
                return;
            }

            $patient = Patient::withTrashed()->find((int)$patientId);

            if (!$patient) {
                $this->redirectWithError($patientId);
                return;
            }

            if ($patient->isDefaultPatient()) {
                Session::flash('info', __('patient.errors.default_patient'));
                $this->redirectRoute('patient.index');
                return;
            }

            $this->form->patient = $patient;
            $this->form->fill($patient->toArray());
        }
    }

    protected function redirectWithError($patientId)
    {
        Session::flash('error', __('patient.errors.not_found', ['id' => $patientId]));
        $this->redirectRoute('patient.index');
    }

    public function save()
    {
        $sanitized = $this->form->sanitized();
        $this->validate();
        try {
            if ($this->form->patient) {
                $this->form->patient->update($sanitized);
                Session::flash('success', __('patient.updated', ['name' => $sanitized['names'] . ' ' . $sanitized['paternal_surname'], 'id' => $this->form->patient->id]));
            } else {
                $patient = Patient::create($sanitized);
                Session::flash('success', __('patient.created', ['name' => $sanitized['names'] . ' ' . $sanitized['paternal_surname'], 'id' => $patient->id]));
            }
            return redirect()->to(route('patient.index'));
        } catch (Exception) {
            Session::flash('error', $this->form->patient ? __('patient.errors.update_failed') : __('patient.errors.creation_failed'));
            return redirect()->to(route('patient.index'));
        }
    }

    public function delete()
    {
        if ($this->form->patient) {
            if ($this->form->patient->trashed()) {
                $this->form->patient->restore();
                Session::flash('success', __('patient.restored', ['id' => $this->form->patient->id]));
            } else {
                $this->form->patient->delete();
                Session::flash('success', __('patient.deleted', ['id' => $this->form->patient->id]));
            }
        }
        return redirect()->to(route('patient.index'));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->patient ? 'patient.edit' : 'patient.create')])
            ->title(__($this->form->patient ? 'views.patient.edit' : 'views.patient.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-3xl" wire:submit="save">
        @if($this->form->patient && $this->form->patient->trashed())
            <x-shared.alert type="info">{{ __('patient.is_deleted') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-3" wire:loading.attr="disabled"
                       wire:target="save, delete">
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.dni') }}</flux:label>
                <flux:input wire:model.live.blur="form.dni" type="text"/>
                <flux:error name="form.dni"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ trans_choice('common.name', 2) }}</flux:label>
                <flux:input wire:model.live.blur="form.names" type="text"/>
                <flux:error name="form.names"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.paternal_surname') }}</flux:label>
                <flux:input wire:model.live.blur="form.paternal_surname" type="text"/>
                <flux:error name="form.paternal_surname"/>
            </flux:field>
            <flux:input wire:model.live.blur="form.maternal_surname" label="{{ __('common.maternal_surname') }}"
                        type="text"/>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.birth_date') }}</flux:label>
                <flux:input wire:model.live.blur="form.birth_date" type="date"/>
                <flux:error name="form.birth_date"/>
            </flux:field>
            <flux:input wire:model.live.blur="form.email" label="{{ __('common.email') }}" type="email"/>
            <flux:input wire:model.live.blur="form.phone" label="{{ __('common.phone') }}" type="text"/>
            <flux:input wire:model.live.blur="form.address" label="{{ __('common.address') }}" type="text"/>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @if($this->form->patient)
                        @canany(['sys.admin', 'patient.delete', 'patient.restore'])
                            <flux:button type="button" variant="primary"
                                         color="{{ $this->form->patient->trashed() ? 'amber' : 'red' }}"
                                         wire:click="delete"
                                         class="w-full md:w-auto" wire:loading.attr="disabled"
                                         wire:target="delete, save">
                                {{ $this->form->patient->trashed() ? __('common.restore') : __('common.delete') }}
                            </flux:button>
                        @endcanany
                    @endif
                    @canany(['sys.admin', 'patient.create', 'patient.update'])
                        <flux:button type="submit" variant="primary" class="w-full md:w-auto md:ml-auto"
                                     wire:loading.attr="disabled" wire:target="delete, save">
                            {{ $this->form->patient ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
