<?php

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Password;
use App\Notifications\PasswordUpdatedNotification;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public ?User $user = null;

    public function mount(?string $userId = null): void
    {
        if ($userId) {
            if (!is_numeric($userId)) {
                $this->redirectWithError($userId);
                return;
            }

            $user = User::withTrashed()->find((int)$userId);

            if (!$user) {
                $this->redirectWithError($userId);
                return;
            }

            $user->load([
                'clinic' => fn($q) => $q->withTrashed(),
                'worker' => fn($q) => $q->withTrashed(),
                'nurse' => fn($q) => $q->withTrashed(),
                'doctor' => fn($q) => $q->withTrashed(),
            ]);

            $this->user = $user;
        }
    }

    protected function redirectWithError($userId): void
    {
        Session::flash('error', __('user.errors.not_found', ['id' => $userId]));
        $this->redirectRoute('user.index');
    }

    public function sendResetEmail(): void
    {
        $status = Password::sendResetLink(['email' => $this->user->email]);

        if ($status === Password::RESET_THROTTLED) {
            Session::flash('warning', __($status));
        } else {
            Session::flash('success', __('auth.password_reset_sent', ['user' => $this->user->username]));
        }
        $this->redirectRoute('user.index');
    }

    public function resetPassword(): void
    {
        try {
            $this->user->forceFill(['password' => trim($this->user->username) . Carbon::now()->year])->update();
            $this->user->notify(new PasswordUpdatedNotification(
                Carbon::now('America/Lima')->format('d/m/Y h:i:s A')
            ));
            Session::flash('success', __('auth.password_changed_alt', ['user' => $this->user->username]));
        } catch (Exception) {
            Session::flash('error', __('auth.errors.password_reset_failed', ['user' => $this->user->username]));
        }
        $this->redirectRoute('user.index');
    }

    public function delete(): void
    {
        try {
            DB::beginTransaction();
            $this->user->load([
                'doctor' => fn($q) => $q->withTrashed(),
                'nurse' => fn($q) => $q->withTrashed(),
                'worker' => fn($q) => $q->withTrashed(),
            ]);

            if ($this->user->doctor) {
                $this->user->trashed() ? $this->user->doctor->restore() : $this->user->doctor->delete();
            }

            if ($this->user->nurse) {
                $this->user->trashed() ? $this->user->nurse->restore() : $this->user->nurse->delete();
            }

            if ($this->user->worker) {
                $this->user->trashed() ? $this->user->worker->restore() : $this->user->worker->delete();
            }

            $this->user->trashed() ? $this->user->restore() : $this->user->delete();

            DB::commit();
            $this->user->refresh();

            Session::flash('success', $this->user->trashed()
                ? __('auth.deleted', ['user' => $this->user->username])
                : __('auth.restored', ['user' => $this->user->username])
            );
        } catch (Exception) {
            DB::rollBack();
            Session::flash('error', __('auth.errors.deletion_failed', ['user' => $this->user->username]));
        }

        $this->redirectRoute('user.index');
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('user.detail', ['id' => $this->user->id, 'username' => mb_strtoupper($this->user->username)])])
            ->title(__('views.user.detail'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-3xl">
        @if($user && $user->trashed())
            <x-shared.alert type="info">{{ __('user.is_deleted_alt') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <div class="col-span-full">
                <div class="flex flex-col items-center">
                    <flux:avatar size="xl" name="{{$user->username}}" color="auto"
                                 src="{{$user->avatar ? Storage::url($user->avatar) : null}}"/>
                </div>
            </div>
            <flux:field>
                <flux:label>{{ __('common.username') }}</flux:label>
                <flux:input readonly value="{{ $user->username }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.email') }}</flux:label>
                <flux:input readonly value="{{ $user->email }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.status') }}</flux:label>
                <flux:input readonly
                            value="{{ $user->deleted_at ? __('common.disabled_entity') : ($user->locked_until !== null ? __('common.locked_account') : __('common.active')) }}"
                            type="text"/>
            </flux:field>
            <flux:input readonly value="{{ $user->clinic?->name }}"
                        label="{{ __('clinic.assigned') }}"
                        type="text"/>
            <flux:input readonly value="{{ match(true) {
                                            $user->worker !== null => ucfirst(mb_strtolower($user->worker->names)),
                                            $user->doctor !== null => ucfirst(mb_strtolower($user->doctor->names)),
                                            $user->nurse !== null  => ucfirst(mb_strtolower($user->nurse->names)),
                                            default                => __('common.null')} }}"
                        label="{{ trans_choice('common.name',2) }}"
                        type="text"/>
            <flux:input readonly value="{{ match(true) {
                                            $user->worker !== null => ucfirst(mb_strtolower($user->worker->paternal_surname)),
                                            $user->doctor !== null => ucfirst(mb_strtolower($user->doctor->paternal_surname)),
                                            $user->nurse !== null  => ucfirst(mb_strtolower($user->nurse->paternal_surname)),
                                            default                => __('common.null')} }}"
                        label="{{ __('common.paternal_surname_alt') }}"
                        type="text"/>
            <div class="col-span-full">
                <flux:input readonly value="{{ match(true) {
                                            $user->worker !== null => $user->worker->address,
                                            $user->doctor !== null => $user->doctor->address,
                                            $user->nurse !== null  => $user->nurse->address,
                                            default                => __('common.null')} }}"
                            label="{{ __('common.address') }}"
                            type="text"/>
            </div>
            <flux:field>
                <flux:label>{{ __('common.dni') }}</flux:label>
                <flux:input.group>
                    <flux:input readonly value="{{ match(true) {
                                            $user->worker !== null => $user->worker->dni,
                                            $user->doctor !== null => $user->doctor->dni,
                                            $user->nurse !== null  => $user->nurse->dni,
                                            default                => __('common.null')} }}"
                                type="text"/>
                    @php
                        $profileRoute = match(true) {
                            $user->worker !== null => route('worker.detail', ['workerId' => $user->worker->id]),
                            $user->doctor !== null => route('doctor.detail', ['doctorId' => $user->doctor->id]),
                            $user->nurse !== null  => route('nurse.detail',  ['nurseId'  => $user->nurse->id]),
                            default                => route('dashboard')
                        };
                    @endphp
                    <flux:button type="button" variant="primary" color="cyan"
                                 icon="ellipsis-horizontal" wire:navigate
                                 href="{{ $profileRoute }}"
                                 wire:target="delete, sendResetEmail, resetPassword"
                                 wire:loading.attr="disabled">
                    </flux:button>
                </flux:input.group>
            </flux:field>

            <flux:field>
                <flux:label>{{ trans_choice('role.role',1) }}</flux:label>
                <flux:input.group>
                    <flux:input readonly value="{{ $user->roles->first()?->name ?? __('common.null') }}"
                                type="text"/>
                    <flux:button type="button" variant="primary" color="cyan"
                                 icon="ellipsis-horizontal" wire:navigate
                                 href="#"
                                 wire:target="delete, sendResetEmail, resetPassword"
                                 wire:loading.attr="disabled">
                    </flux:button>
                </flux:input.group>
            </flux:field>

            <flux:input readonly value="{{ match(true) {
                                            $user->worker !== null => Carbon::createFromFormat('Y-m-d',$user->worker->hired_at)->timezone('America/Lima')->format('d/m/Y') ?? __('common.null'),
                                            $user->doctor !== null => Carbon::createFromFormat('Y-m-d',$user->doctor->hired_at)->timezone('America/Lima')->format('d/m/Y') ?? __('common.null'),
                                            $user->nurse !== null  => Carbon::createFromFormat('Y-m-d',$user->nurse->hired_at)->timezone('America/Lima')->format('d/m/Y') ?? __('common.null'),
                                            default                => __('common.null')} }}"
                        label="{{ __('common.hired_at') }}"
                        type="text"/>
            <flux:input readonly value="{{ $user->email ?? __('common.null') }}" label="{{ __('common.email') }}"
                        type="email"/>
            <flux:input readonly
                        value="{{ $user->created_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.created_at') }}" type="text"/>
            <flux:input readonly
                        value="{{ $user->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.updated_at_alt') }}" type="text"/>
            @canany(['sys.admin','user.resetEmail'])
                <flux:button class="w-full" color="cyan" variant="primary"
                             wire:target="delete, sendResetEmail, resetPassword"
                             wire:click="sendResetEmail">{{__('auth.send_reset_email')}}</flux:button>
            @endcanany
            @canany(['sys.admin','user.resetPassword'])
                <flux:button class="w-full" color="zinc" variant="primary"
                             wire:target="delete, sendResetEmail, resetPassword"
                             wire:click="resetPassword">{{__('auth.reset_password_alt')}}</flux:button>
            @endcanany
            @canany(['sys.admin', 'user.delete', 'user.restore'])
                <flux:button type="button" variant="primary" color="{{$this->user->trashed() ? 'amber' : 'red'}}"
                             class="w-full" wire:click="delete"
                             wire:target="delete, sendResetEmail, resetPassword">
                    {{ $this->user->trashed() ? __('common.restore') : __('common.delete') }}
                </flux:button>
            @endcanany
            @canany(['sys.admin', 'user.update'])
                <flux:button type="button" variant="primary" class="w-full" wire:navigate
                             href="{{route('user.edit',['userId' => $this->user->id])}}"
                             wire:target="delete, sendResetEmail, resetPassword"
                             wire:loading.attr="disabled">
                    {{ __('common.edit') }}
                </flux:button>
            @endcanany
        </flux:fieldset>
    </div>
</div>
