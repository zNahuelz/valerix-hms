<?php

use Livewire\Component;
use App\Models\Patient;
use Illuminate\Support\Carbon;

new class extends Component
{
    public ?Patient $patient = null;

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

            $patient->load(['createdBy', 'updatedBy']);

            $this->patient = $patient;
        }
    }

    protected function redirectWithError($patientId)
    {
        Session::flash('error', __('patient.errors.not_found', ['id' => $patientId]));
        $this->redirectRoute('patient.index');
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('patient.detail', ['id' => $this->patient->id, 'name' => ucwords(strtolower($this->patient->names))
                .' '.
                ucwords(strtolower($this->patient->paternal_surname))])])
            ->title(__('views.patient.detail'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-3xl">
        @if($patient && $patient->trashed())
            <x-shared.alert type="info">{{ __('patient.is_deleted_alt') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <flux:field>
                <flux:label>{{ __('common.dni') }}</flux:label>
                <flux:input readonly value="{{ $patient->dni }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input readonly value="{{ $patient->names }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.paternal_surname') }}</flux:label>
                <flux:input readonly value="{{ $patient->paternal_surname }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.maternal_surname') }}</flux:label>
                <flux:input readonly value="{{ $patient->maternal_surname ?? __('common.null') }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.birth_date') }}</flux:label>
                <flux:input readonly value="{{ Carbon::createFromFormat('Y-m-d',$patient->birth_date)->timezone('America/Lima')->format('d/m/Y') ?? __('common.null') }}" type="text"/>
            </flux:field>
            <flux:input readonly value="{{ $patient->email ?? __('common.null') }}" label="{{ __('common.email') }}" type="email"/>
            <flux:input readonly value="{{ $patient->phone ?? __('common.null') }}" label="{{ __('common.phone') }}" type="text"/>
            <flux:input readonly value="{{ $patient->address ?? __('common.null') }}" label="{{ __('common.address') }}"
                        type="text"/>
            <flux:input readonly value="{{ $patient->createdBy->username ?? __('common.inserted_by_null') }}"
                        label="{{ __('common.created_by') }}" type="text"/>
            <flux:input readonly value="{{ $patient->updatedBy->username ?? __('common.inserted_by_null') }}"
                        label="{{ __('common.updated_by') }}" type="text"/>
            <flux:input readonly
                        value="{{ $patient->created_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.created_at') }}" type="text"/>
            <flux:input readonly
                        value="{{ $patient->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.updated_at_alt') }}" type="text"/>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @if(!$patient->isDefaultPatient())
                    @canany(['sys.admin', 'patient.update', 'patient.delete', 'patient.restore'])
                        <flux:button type="button" variant="primary" class="w-full md:w-auto md:ml-auto" wire:navigate
                                     href="{{ route('patient.edit', ['patientId' => $patient->id]) }}">
                            {{ __('common.edit') }}
                        </flux:button>
                    @endcanany
                        @endif
                </div>
            </div>
        </flux:fieldset>
    </div>
</div>
