<?php

use Livewire\Component;
use App\Models\Nurse;
use App\Models\User;
use App\Models\Clinic;
use App\Livewire\Forms\NurseForm;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

new class extends Component
{
    public NurseForm $form;
    public array $clinics = [];
    public $nurseRoleId = 0;

    public function mount(?string $nurseId = null): void
    {
        if(!Clinic::whereNull('deleted_at')->exists()){
            Session::flash('error', __('nurse.errors.creation_disabled_empty_clinics'));
            $this->redirectRoute('nurse.index');
            return;
        }
        $this->clinics = Clinic::select(['id','name'])->whereNull('deleted_at')->orderBy('name')->get()->toArray();
        if(!Role::exists()){
            Session::flash('error', __('nurse.errors.creation_disabled_empty_roles'));
            $this->redirectRoute('nurse.index');
            return;
        }
        $this->nurseRoleId = Role::whereIn('name', ['ENFERMERA', 'NURSE'])
            ->orderBy('name')
            ->value('id') ?? 0;

        if($this->nurseRoleId == 0){
            Session::flash('error', __('nurse.errors.creation_disabled_nurse_role_not_found'));
            $this->redirectRoute('nurse.index');
            return;
        }

        if(!$nurseId){
            $this->form->clinic_id = $this->clinics[0]['id'];
            $this->form->role_id = $this->nurseRoleId;
        }
        if ($nurseId) {
            if (!is_numeric($nurseId)) {
                $this->redirectWithError($nurseId);
                return;
            }

            $nurse = Nurse::withTrashed()->find((int)$nurseId);

            if (!$nurse) {
                $this->redirectWithError($nurseId);
                return;
            }
            $nurse->load(['user' => function($query) {
                $query->withTrashed();
            }]);
            if(auth()->user()->is($nurse->user)){
                Session::flash('warning', __('nurse.errors.editing_session'));
                $this->redirectRoute('nurse.index');
                return;
            }
            $this->form->email = $nurse->user?->email ?? '';
            $this->form->position = strtoupper($nurse->position);
            $this->form->role_id = $nurse->user->roles->first()->id;
            $this->form->clinic_id = $nurse->clinic->id;
            $this->form->nurse = $nurse;
            $this->form->fill($nurse->toArray());
        }
    }

    protected function redirectWithError($nurseId)
    {
        Session::flash('error', __('nurse.errors.not_found', ['id' => $nurseId]));
        $this->redirectRoute('nurse.index');
    }

    public function save()
    {
        $sanitized = $this->form->sanitized();
        $this->validate();
        if ($this->form->nurse) {
            $this->form->nurse->load(['user' => fn($q) => $q->withTrashed()]);
            $this->form->nurse->update($sanitized);
            $this->form->nurse->user->update([
                'email' => $sanitized['email'],
                'clinic_id' => $sanitized['clinic_id'],
            ]);
            //TODO: Trigger user updated email.
            Session::flash('success', __('nurse.updated', ['name' => $sanitized['names'] .' '. $sanitized['paternal_surname'], 'id' => $this->form->nurse->id]));
        } else {
            $user = User::create([
                'username' => $this->generateUsername($sanitized['names'],$sanitized['paternal_surname'],$sanitized['dni']),
                'password' => strrev($this->generateUsername($sanitized['names'],$sanitized['paternal_surname'],$sanitized['dni'])),
                'email' => $sanitized['email'],
                'avatar' => null,
                'clinic_id' => $sanitized['clinic_id'],
            ]);
            $role = Role::findById($this->nurseRoleId);
            $user->assignRole($role);
            $sanitized['user_id'] = $user->id;
            $nurse = Nurse::create($sanitized);
            //TODO: Trigger user created email.
            Session::flash('success', __('nurse.created', ['name' => $sanitized['names'] .' '. $sanitized['paternal_surname'], 'id' => $nurse->id, 'username' => $user->username]));
        }
        return redirect()->to(route('nurse.index'));
    }

    public function delete()
    {
        if ($this->form->nurse) {
            if ($this->form->nurse->trashed()) {
                $this->form->nurse->restore();
                $this->form->nurse->user()->withTrashed()->first()->restore();
                Session::flash('success', __('nurse.restored', ['id' => $this->form->nurse->id]));
            } else {
                $this->form->nurse->delete();
                $this->form->nurse->user()->withTrashed()->first()->delete();
                Session::flash('success', __('nurse.deleted', ['id' => $this->form->nurse->id]));
            }
        }
        return redirect()->to(route('nurse.index'));
    }

    protected function generateUsername($names, $paternalSurname, $dni)
    {
        return strtoupper(substr($names, 0, 1) . $dni . substr($paternalSurname, 0, 1));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->nurse ? 'nurse.edit' : 'nurse.create')])
            ->title(__($this->form->nurse ? 'views.nurse.edit' : 'views.nurse.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-3xl" wire:submit="save">
        @if($this->form->nurse && $this->form->nurse->trashed())
            <x-shared.alert type="info">{{ __('nurse.is_deleted') }}</x-shared.alert>
        @endif
        @if(!$this->form->nurse)
            <x-shared.alert type="info">{{ __('nurse.username_generation') }}</x-shared.alert>
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
            <flux:input wire:model.live.blur="form.maternal_surname" label="{{ __('common.maternal_surname') }}" type="text"/>
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
                    @if($this->form->nurse)
                        @canany(['sys.admin', 'nurse.delete', 'nurse.restore'])
                            <flux:button type="button" variant="primary"
                                         color="{{ $this->form->nurse->trashed() ? 'amber' : 'red' }}"
                                         wire:click="delete"
                                         class="w-full md:w-auto" wire:loading.attr="disabled"
                                         wire:target="delete, save">
                                {{ $this->form->nurse->trashed() ? __('common.restore') : __('common.delete') }}
                            </flux:button>
                        @endcanany
                    @endif
                    @canany(['sys.admin', 'nurse.create', 'nurse.update'])
                        <flux:button type="submit" variant="primary" class="w-full md:w-auto md:ml-auto"
                                     wire:loading.attr="disabled" wire:target="delete, save">
                            {{ $this->form->nurse ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
