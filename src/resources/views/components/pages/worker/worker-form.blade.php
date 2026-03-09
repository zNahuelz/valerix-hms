<?php

use Livewire\Component;
use App\Models\Worker;
use App\Models\User;
use App\Models\Clinic;
use App\Livewire\Forms\WorkerForm;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

new class extends Component {
    public WorkerForm $form;
    public array $clinics = [];
    public array $roles = [];
    public array $positions = [
        'GERENTE', 'ENCARGADO', 'SECRETARIA', 'VENDEDOR', 'SUPERVISOR', 'OTRO',
    ];

    public function mount(?string $workerId = null): void
    {
        if (!Clinic::whereNull('deleted_at')->exists()) {
            Session::flash('error', __('worker.errors.creation_disabled_empty_clinics'));
            $this->redirectRoute('worker.index');
            return;
        }
        $this->clinics = Clinic::select(['id', 'name'])->whereNull('deleted_at')->orderBy('name')->get()->toArray();
        if (!Role::exists()) {
            Session::flash('error', __('worker.errors.creation_disabled_empty_roles'));
            $this->redirectRoute('worker.index');
            return;
        }
        $this->roles = Role::select(['id', 'name'])->whereNot('name', 'DOCTOR')->whereNot('name', 'ENFERMERA')->orderBy('name')->get()->toArray();
        if (!$workerId) {
            $this->form->clinic_id = $this->clinics[0]['id'];
            $this->form->role_id = $this->roles[0]['id'];
            $this->form->position = $this->positions[0];
        }
        if ($workerId) {
            if (!is_numeric($workerId)) {
                $this->redirectWithError($workerId);
                return;
            }

            $worker = Worker::withTrashed()->find((int)$workerId);

            if (!$worker) {
                $this->redirectWithError($workerId);
                return;
            }
            $worker->load(['user' => function ($query) {
                $query->withTrashed();
            }]);
            if (auth()->user()->is($worker->user)) {
                Session::flash('warning', __('worker.errors.editing_session'));
                $this->redirectRoute('worker.index');
                return;
            }
            if ($worker->user?->hasRole('ENFERMERA')) {
                Session::flash('error', __('worker.errors.editing_nurse'));
                $this->redirectRoute('worker.index');
                return;
            }
            $this->form->email = $worker->user->email ?? '';
            $this->form->position = strtoupper($worker->position);
            $this->form->role_id = $worker->user?->roles->first()->id ?? $this->roles[0]['id'];
            $this->form->clinic_id = $worker->clinic?->id;
            $this->form->worker = $worker;
            $this->form->fill($worker->toArray());
        }
    }

    protected function redirectWithError($workerId)
    {
        Session::flash('error', __('worker.errors.not_found', ['id' => $workerId]));
        $this->redirectRoute('worker.index');
    }

    public function save()
    {
        $sanitized = $this->form->sanitized();
        $this->validate();
        try {
            if ($this->form->worker) {
                $this->form->worker->load(['user' => fn($q) => $q->withTrashed()]);
                $this->form->worker->update($sanitized);
                $this->form->worker->user->update([
                    'email' => $sanitized['email'],
                    'clinic_id' => $sanitized['clinic_id'],
                ]);
                $role = Role::findById($sanitized['role_id']);
                $this->form->worker->user->syncRoles([$role]);
                //TODO: Trigger user updated email.
                Session::flash('success', __('worker.updated', ['name' => $sanitized['names'] . ' ' . $sanitized['paternal_surname'], 'id' => $this->form->worker->id]));
            } else {
                $user = User::create([
                    'username' => $this->generateUsername($sanitized['names'], $sanitized['paternal_surname'], $sanitized['dni']),
                    'password' => strrev($this->generateUsername($sanitized['names'], $sanitized['paternal_surname'], $sanitized['dni'])),
                    'email' => $sanitized['email'],
                    'avatar' => null,
                    'clinic_id' => $sanitized['clinic_id'],
                ]);
                $role = Role::findById($sanitized['role_id']);
                $user->assignRole($role);
                $sanitized['user_id'] = $user->id;
                $worker = Worker::create($sanitized);
                //TODO: Trigger user created email.
                Session::flash('success', __('worker.created', ['name' => $sanitized['names'] . ' ' . $sanitized['paternal_surname'], 'id' => $worker->id, 'username' => $user->username]));
            }
            return redirect()->to(route('worker.index'));
        } catch (Exception) {
            Session::flash('error', $this->form->worker ? __('worker.errors.update_failed') : __('worker.errors.creation_failed'));
            return redirect()->to(route('worker.index'));
        }
    }

    public function delete()
    {
        if ($this->form->worker) {
            if ($this->form->worker->trashed()) {
                $this->form->worker->restore();
                $this->form->worker->user()->withTrashed()->first()->restore();
                Session::flash('success', __('worker.restored', ['id' => $this->form->worker->id]));
            } else {
                $this->form->worker->delete();
                $this->form->worker->user()->withTrashed()->first()->delete();
                Session::flash('success', __('worker.deleted', ['id' => $this->form->worker->id]));
            }
        }
        return redirect()->to(route('worker.index'));
    }

    protected function generateUsername($names, $paternalSurname, $dni)
    {
        return strtoupper(substr($names, 0, 1) . $dni . substr($paternalSurname, 0, 1));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->worker ? 'worker.edit' : 'worker.create')])
            ->title(__($this->form->worker ? 'views.worker.edit' : 'views.worker.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-3xl" wire:submit="save">
        @if($this->form->worker && $this->form->worker->trashed())
            <x-shared.alert type="info">{{ __('worker.is_deleted') }}</x-shared.alert>
        @endif
        @if(!$this->form->worker)
            <x-shared.alert type="info">{{ __('worker.username_generation') }}</x-shared.alert>
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
                <flux:label>{{ __('common.position') }}</flux:label>
                <flux:select wire:model.live.blur="form.position">
                    @foreach($positions as $p)
                        <flux:select.option
                            value="{{$p}}">{{$p}}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="form.position"/>
            </flux:field>
            <div class="col-span-full">
                <flux:field>
                    <flux:label badge="{{ __('common.required') }}">{{ __('common.address') }}</flux:label>
                    <flux:input wire:model.live.blur="form.address" type="text"/>
                    <flux:error name="form.address"/>
                </flux:field>
            </div>
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
            <flux:field>
                <flux:label>{{ trans_choice('role.role',1) }}</flux:label>
                <flux:select wire:model.live.blur="form.role_id">
                    @foreach($roles as $role)
                        <flux:select.option
                            value="{{$role['id']}}">{{$role['name']}}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="form.role_id"/>
            </flux:field>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @if($this->form->worker)
                        @canany(['sys.admin', 'worker.delete', 'worker.restore'])
                            <flux:button type="button" variant="primary"
                                         color="{{ $this->form->worker->trashed() ? 'amber' : 'red' }}"
                                         wire:click="delete"
                                         class="w-full md:w-auto" wire:loading.attr="disabled"
                                         icon="{{ $form->worker->trashed() ? 'arrow-path' : 'trash' }}"
                                         wire:target="delete, save">
                                {{ $this->form->worker->trashed() ? __('common.restore') : __('common.delete') }}
                            </flux:button>
                        @endcanany
                    @endif
                    @canany(['sys.admin', 'worker.create', 'worker.update'])
                        <flux:button type="submit" variant="primary" class="w-full md:w-auto md:ml-auto"
                                     wire:loading.attr="disabled" wire:target="delete, save">
                            {{ $this->form->worker ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
