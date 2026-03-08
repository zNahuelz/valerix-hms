<?php

use Livewire\Component;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Clinic;
use App\Livewire\Forms\Doctor\DoctorEditForm;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

new class extends Component {
    public DoctorEditForm $form;
    public array $clinics = [];
    public int $doctorRoleId = 0;

    public function mount(?string $doctorId = null): void
    {
        if (!Clinic::whereNull('deleted_at')->exists()) {
            Session::flash('error', __('doctor.errors.creation_disabled_empty_clinics'));
            $this->redirectRoute('doctor.index');
            return;
        }
        $this->clinics = Clinic::select(['id', 'name'])->whereNull('deleted_at')->orderBy('name')->get()->toArray();
        if (!Role::exists()) {
            Session::flash('error', __('doctor.errors.creation_disabled_empty_roles'));
            $this->redirectRoute('doctor.index');
            return;
        }
        $this->doctorRoleId = Role::where('name', 'DOCTOR')
            ->orderBy('name')
            ->value('id') ?? 0;

        if ($this->doctorRoleId === 0) {
            Session::flash('error', __('doctor.errors.creation_disabled_doctor_role_not_found'));
            $this->redirectRoute('doctor.index');
            return;
        }

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
            $doctor->load(['user' => function ($query) {
                $query->withTrashed();
            }]);
            if (auth()->user()->is($doctor->user)) {
                Session::flash('warning', __('doctor.errors.editing_session'));
                $this->redirectRoute('doctor.index');
                return;
            }
            $this->form->email = $doctor->user?->email ?? '';
            $this->form->role_id = $this->doctorRoleId;
            $this->form->clinic_id = $doctor->clinic->id;
            $this->form->doctor = $doctor;
            $this->form->fill($doctor->toArray());
        }
    }

    protected function redirectWithError($doctorId)
    {
        Session::flash('error', __('doctor.errors.not_found', ['id' => $doctorId]));
        $this->redirectRoute('doctor.index');
    }

    public function save()
    {
        $sanitized = $this->form->sanitized();
        $this->validate();
        if ($this->form->doctor) {
            $this->form->doctor->update($sanitized);
            $this->form->doctor->user->update([
                'email' => $sanitized['email'],
                'clinic_id' => $sanitized['clinic_id'],
            ]);
            //TODO: Trigger user updated email.
            Session::flash('success', __('doctor.updated', ['name' => $sanitized['names'] . ' ' . $sanitized['paternal_surname'], 'id' => $this->form->doctor->id]));
        }
        return redirect()->to(route('doctor.index'));
    }

    public function delete()
    {
        if ($this->form->doctor) {
            if ($this->form->doctor->trashed()) {
                $this->form->doctor->restore();
                $this->form->doctor->user()->withTrashed()->first()->restore();
                Session::flash('success', __('doctor.restored', ['id' => $this->form->doctor->id]));
            } else {
                $this->form->doctor->delete();
                $this->form->doctor->user()->withTrashed()->first()->delete();
                Session::flash('success', __('doctor.deleted', ['id' => $this->form->doctor->id]));
            }
        }
        return redirect()->to(route('doctor.index'));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('doctor.edit')])
            ->title(__('views.doctor.edit'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-3xl" wire:submit="save">
        @if($this->form->doctor && $this->form->doctor->trashed())
            <x-shared.alert type="info">{{ __('doctor.is_deleted') }}</x-shared.alert>
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
                <flux:label badge="{{ __('common.required') }}">{{ __('common.hired_at') }}</flux:label>
                <flux:input wire:model.live.blur="form.hired_at" type="date"/>
                <flux:error name="form.hired_at"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.email') }}</flux:label>
                <flux:input wire:model.live.blur="form.email" type="email"/>
                <flux:error name="form.email"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.phone') }}</flux:label>
                <flux:input wire:model.live.blur="form.phone" type="text"/>
                <flux:error name="form.phone"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ trans_choice('clinic.clinic',1) }}</flux:label>
                <flux:select wire:model.live.blur="form.clinic_id">
                    @foreach($clinics as $clinic)
                        <flux:select.option
                            value="{{$clinic['id']}}">{{$clinic['name']}}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="form.clinic_id"/>
            </flux:field>
            <div class="col-span-full">
                <flux:field>
                    <flux:label badge="{{ __('common.required') }}">{{ __('common.address') }}</flux:label>
                    <flux:input wire:model.live.blur="form.address" type="text"/>
                    <flux:error name="form.address"/>
                </flux:field>
            </div>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @if($this->form->doctor)
                        @canany(['sys.admin', 'doctor.delete', 'doctor.restore'])
                            <flux:button type="button" variant="primary"
                                         color="{{ $this->form->doctor->trashed() ? 'amber' : 'red' }}"
                                         wire:click="delete"
                                         class="w-full md:w-auto" wire:loading.attr="disabled"
                                         wire:target="delete, save">
                                {{ $this->form->doctor->trashed() ? __('common.restore') : __('common.delete') }}
                            </flux:button>
                        @endcanany
                    @endif
                    @canany(['sys.admin', 'doctor.create', 'doctor.update'])
                        <flux:button type="submit" variant="primary" class="w-full md:w-auto md:ml-auto"
                                     wire:loading.attr="disabled" wire:target="delete, save">
                            {{ __('common.update') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
