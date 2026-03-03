<?php

use Livewire\Component;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\User;
use App\Models\Clinic;
use App\Livewire\Forms\Doctor\DoctorCreateForm;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public DoctorCreateForm $form;
    public array $clinics = [];
    public int $doctorRoleId = 0;
    public int $currentStep = 1;
    public int $totalSteps = 3;

    public function mount(): void
    {
        if(!Clinic::whereNull('deleted_at')->exists()){
            Session::flash('error', __('doctor.errors.creation_disabled_empty_clinics'));
            $this->redirectRoute('doctor.index');
            return;
        }
        $this->clinics = Clinic::select(['id','name'])->whereNull('deleted_at')->orderBy('name')->get()->toArray();
        if(!Role::exists()){
            Session::flash('error', __('doctor.errors.creation_disabled_empty_roles'));
            $this->redirectRoute('doctor.index');
            return;
        }
        $this->doctorRoleId = Role::where('name', 'DOCTOR')
            ->orderBy('name')
            ->value('id') ?? 0;

        if($this->doctorRoleId === 0){
            Session::flash('error', __('doctor.errors.creation_disabled_doctor_role_not_found'));
            $this->redirectRoute('doctor.index');
            return;
        }

        $this->form->availabilities = collect(range(1, 5))->map(fn($day) => [
            'weekday'    => $day,
            'start_time' => '08:00',
            'end_time'   => '17:00',
            'break_start'=> '12:00',
            'break_end'  => '13:00',
            'is_active'  => true,
        ])->toArray();

        $this->form->clinic_id = $this->clinics[0]['id'];
        $this->form->role_id = $this->doctorRoleId;
    }

    public function nextStep(): void
    {
        if ($this->currentStep < 3) {
            $this->form->validateStep($this->currentStep);
        }

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function addAvailability(): void
    {
        if (count($this->form->availabilities) < 7) {
            $nextWeekday = max(array_column($this->form->availabilities, 'weekday')) + 1;
            $this->form->availabilities[] = [
                'weekday'     => $nextWeekday,
                'start_time'  => '08:00',
                'end_time'    => '17:00',
                'break_start' => '12:00',
                'break_end'   => '13:00',
                'is_active'   => true,
            ];
        }
    }

    public function save()
    {
        try {
            $this->form->validateStep(1);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->currentStep = 1;
            throw $e;
        }
        try {
            $this->form->validateStep(2);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->currentStep = 2;
            throw $e;
        }
        try{
            DB::beginTransaction();
            $sanitized = $this->form->sanitized();
            $user = User::create([
                'username' => $this->generateUsername($sanitized['names'],$sanitized['paternal_surname'],$sanitized['dni']),
                'password' => strrev($this->generateUsername($sanitized['names'],$sanitized['paternal_surname'],$sanitized['dni'])),
                'email' => $sanitized['email'],
                'avatar' => null,
                'clinic_id' => $sanitized['clinic_id'],
            ]);
            $role = Role::findById($this->doctorRoleId);
            $user->assignRole($role);
            $doctor = Doctor::create([
                'names' => $sanitized['names'],
                'paternal_surname' => $sanitized['paternal_surname'],
                'maternal_surname' => $sanitized['maternal_surname'],
                'dni' => $sanitized['dni'],
                'phone' => $sanitized['phone'],
                'address' => $sanitized['address'],
                'hired_at' => $sanitized['hired_at'],
                'clinic_id' => $sanitized['clinic_id'],
                'user_id' => $user->id,
            ]);

            foreach($sanitized['availabilities'] as $av)
            {
                DoctorAvailability::create([
                    'doctor_id' => $doctor->id,
                    'weekday' => $av['weekday'],
                    'start_time' => $av['start_time'],
                    'end_time' => $av['end_time'],
                    'break_start' => $av['break_start'],
                    'break_end' => $av['break_end'],
                    'is_active' => $av['is_active'],
                ]);
            }
            DB::commit();
            //TODO: Trigger doctor created email.
            Session::flash('success', __('doctor.created', ['name' => $sanitized['names'] .' '. $sanitized['paternal_surname'], 'id' => $doctor->id, 'username' => $user->username]));
            return redirect()->to(route('doctor.index'));
        }
        catch(Exception $e){
            DB::rollBack();
            Session::flash('error',__('doctor.errors.creation_failed'));
            return redirect()->to(route('doctor.index'));
        }
    }

    protected function generateUsername($names, $paternalSurname, $dni)
    {
        return strtoupper(substr($names, 0, 1) . $dni . substr($paternalSurname, 0, 1));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('doctor.create')])
            ->title(__('views.doctor.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-3xl">
        <x-shared.alert type="info">{{ __('doctor.username_generation') }}</x-shared.alert>
        <div class="my-4 text-center md:text-end">
            <flux:badge size="lg" color="emerald">
                <span class="font-bold uppercase">
                    {{ __('common.step_one_of', ['step' => $currentStep, 'total' => $totalSteps]) }}
                </span>
            </flux:badge>
        </div>
        {{-- ────────────────── STEP 1: Doctor Info ────────────────── --}}
        @if ($currentStep === 1)
            <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-3">
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
                <flux:input wire:model.live.blur="form.maternal_surname"
                            label="{{ __('common.maternal_surname') }}" type="text"/>
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
                    <flux:label>{{ trans_choice('clinic.clinic', 1) }}</flux:label>
                    <flux:select wire:model.live.blur="form.clinic_id">
                        @foreach ($clinics as $clinic)
                            <flux:select.option value="{{ $clinic['id'] }}">{{ $clinic['name'] }}</flux:select.option>
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
                {{-- Step 1 footer --}}
                <div class="col-span-full flex justify-end">
                    <flux:button type="button" variant="primary" wire:click="nextStep">
                        {{ __('common.next') }}
                    </flux:button>
                </div>
            </flux:fieldset>
        @endif

        {{-- ────────────────── STEP 2: Availabilities ────────────────── --}}
        @if ($currentStep === 2)
            <flux:fieldset class="space-y-4">
                <div class="flex items-center justify-between">
                    <flux:legend>{{ __('common.set_schedule') }}</flux:legend>
                    @if (count($form->availabilities) < 7)
                        <flux:button type="button" size="sm" wire:click="addAvailability">
                            + {{ __('common.add_day') }}
                        </flux:button>
                    @endif
                </div>
                <flux:error name="form.availabilities"/>
                @foreach ($form->availabilities as $index => $slot)
                    <flux:card class="space-y-4">
                        {{-- Card header --}}
                        <div class="flex items-center justify-between">
                            <flux:heading class="flex items-center gap-2">
                                {{ __('common.weekdays')[$slot['weekday']] }}
                            </flux:heading>
                            <flux:checkbox
                                wire:model="form.availabilities.{{ $index }}.is_active"
                                :label="__('common.active')"
                            />
                        </div>
                        {{-- Time fields --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                            <flux:field>
                                <flux:label>{{ __('common.start_time') }}</flux:label>
                                <flux:input type="time"
                                            wire:model="form.availabilities.{{ $index }}.start_time"/>
                                <flux:error name="form.availabilities.{{ $index }}.start_time"/>
                            </flux:field>
                            <flux:field>
                                <flux:label>{{ __('common.end_time') }}</flux:label>
                                <flux:input type="time"
                                            wire:model="form.availabilities.{{ $index }}.end_time"/>
                                <flux:error name="form.availabilities.{{ $index }}.end_time"/>
                            </flux:field>
                            <flux:field>
                                <flux:label>{{ __('common.break_start') }}</flux:label>
                                <flux:input type="time"
                                            wire:model="form.availabilities.{{ $index }}.break_start"/>
                                <flux:error name="form.availabilities.{{ $index }}.break_start"/>
                            </flux:field>
                            <flux:field>
                                <flux:label>{{ __('common.break_end') }}</flux:label>
                                <flux:input type="time"
                                            wire:model="form.availabilities.{{ $index }}.break_end"/>
                                <flux:error name="form.availabilities.{{ $index }}.break_end"/>
                            </flux:field>
                        </div>
                    </flux:card>
                @endforeach
                {{-- Step 2 footer --}}
                <div class="flex flex-col md:flex-row md:justify-between gap-2 pt-2">
                    <flux:button type="button" wire:click="previousStep">
                        {{ __('common.back') }}
                    </flux:button>
                    @canany(['sys.admin', 'doctor.create'])
                        <flux:button type="button" variant="primary" wire:click="nextStep">
                            {{ __('common.next') }}
                        </flux:button>
                    @endcanany
                </div>
            </flux:fieldset>
        @endif

        {{-- ────────────────── STEP 3: Review & Confirm ────────────────── --}}
        @if ($currentStep === 3)
            <flux:fieldset class="space-y-4">
                <flux:legend>{{ __('common.review') }}</flux:legend>
                {{-- Doctor Info --}}
                <flux:card class="space-y-4">
                    <flux:heading>{{ __('doctor.doctor_info') }}</flux:heading>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div>
                            <flux:text class="text-zinc-400">{{ __('common.dni') }}</flux:text>
                            <flux:text class="font-medium">{{ $form->dni }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-400">{{ trans_choice('common.name', 2) }}</flux:text>
                            <flux:text class="font-medium">{{ $form->names }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-400">{{ __('common.paternal_surname') }}</flux:text>
                            <flux:text class="font-medium">{{ $form->paternal_surname }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-400">{{ __('common.maternal_surname') }}</flux:text>
                            <flux:text class="font-medium">{{ $form->maternal_surname ?: '—' }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-400">{{ __('common.email') }}</flux:text>
                            <flux:text class="font-medium">{{ $form->email }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-400">{{ __('common.phone') }}</flux:text>
                            <flux:text class="font-medium">{{ $form->phone }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-400">{{ __('common.address') }}</flux:text>
                            <flux:text class="font-medium">{{ $form->address }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-400">{{ __('common.hired_at') }}</flux:text>
                            <flux:text class="font-medium">{{ $form->hired_at }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-400">{{ trans_choice('clinic.clinic', 1) }}</flux:text>
                            <flux:text class="font-medium">
                                {{ collect($clinics)->firstWhere('id', $form->clinic_id)['name'] ?? '—' }}
                            </flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-400">{{ __('common.username') }}</flux:text>
                            <flux:text class="font-medium">{{ $this->generateUsername($this->form->names,$this->form->paternal_surname,$this->form->dni) ?? __('common.null') }}</flux:text>
                        </div>
                    </div>
                </flux:card>
                {{-- Availabilities --}}
                <flux:card class="space-y-4">
                    <flux:heading>{{ __('common.schedule_settings') }}</flux:heading>
                    <div class="space-y-2">
                        @foreach ($form->availabilities as $slot)
                            <div class="grid grid-cols-1 sm:grid-cols-5 gap-2 items-center text-sm border-b pb-2 last:border-0 last:pb-0">
                                <flux:text class="font-semibold">
                                    {{ __('common.weekdays')[$slot['weekday']] }}
                                    @if(!$slot['is_active'])
                                        <flux:badge size="sm" color="red">{{ __('common.inactive') }}</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="emerald">{{ __('common.active') }}</flux:badge>
                                    @endif
                                </flux:text>
                                <div>
                                    <flux:text class="text-zinc-400">{{ __('common.start_time') }}</flux:text>
                                    <flux:text>{{ $slot['start_time'] }}</flux:text>
                                </div>
                                <div>
                                    <flux:text class="text-zinc-400">{{ __('common.end_time') }}</flux:text>
                                    <flux:text>{{ $slot['end_time'] }}</flux:text>
                                </div>
                                <div>
                                    <flux:text class="text-zinc-400">{{ __('common.break_start') }}</flux:text>
                                    <flux:text>{{ $slot['break_start'] }}</flux:text>
                                </div>
                                <div>
                                    <flux:text class="text-zinc-400">{{ __('common.break_end') }}</flux:text>
                                    <flux:text>{{ $slot['break_end'] }}</flux:text>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </flux:card>
                {{-- Step 3 footer --}}
                <div class="flex flex-col md:flex-row md:justify-between gap-2 pt-2">
                    <flux:button type="button" wire:click="previousStep">
                        {{ __('common.back') }}
                    </flux:button>

                    @canany(['sys.admin', 'doctor.create'])
                        <flux:button type="button" variant="primary" wire:click="save">
                            {{ __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </flux:fieldset>
        @endif
    </div>
</div>
