<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Livewire\Forms\Auth\LoginForm;

new class extends Component {
    public LoginForm $form;

    public string $loginImage = '';

    public function mount(): void
    {
        $this->loginImage = 'img' . rand(1, 10) . '.jpg';
    }

    public function login()
    {
        $this->validate();

        $user = User::where('username', $this->form->username)->first();

        if (!$user) {
            $this->addError('form.username', __('auth.errors.invalid_credentials'));
            return;
        }

        // Lockout check
        if (
            $user->lockout_enabled &&
            $user->locked_until &&
            $user->locked_until->isFuture()
        ) {

            $this->addError(
                'form.username',
                __('auth.errors.account_locked', [
                    'lockedUntil' => $user->locked_until
                        ->timezone('America/Lima')
                        ->format('d-m-Y h:i A'),
                ])
            );

            return;
        }

        if (
            !Auth::attempt([
                'username' => $this->form->username,
                'password' => $this->form->password,
            ], $this->form->remember_me)
        ) {

            $user->increment('failed_attempts');

            if ($user->lockout_enabled && $user->failed_attempts >= 5) {
                $user->update([
                    'locked_until' => Carbon::now()->addMinutes(15),
                    'failed_attempts' => 0,
                ]);
            }

            $this->addError('form.username', __('auth.errors.invalid_credentials'));
            return;
        }

        $user->update([
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);

        session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function render()
    {
        return $this->view()->layout('layouts::guest')->title(__('views.login'));
    }
};
?>

<div class="flex min-h-svh flex-col items-center justify-center p-6 md:p-10">
    <div class="w-full max-w-sm md:max-w-4xl">
        <flux:card class="overflow-hidden p-0">
            <div class="md:flex">
                <div class="p-6 md:w-1/2 space-y-6">
                    <form wire:submit.prevent="login">
                        <div>
                            <flux:heading size="xl" class="font-bold! text-center">
                                {{ __('auth.welcome') }}
                            </flux:heading>

                            <flux:text class="mt-2 text-center">
                                {{ __('auth.login_title') }}
                            </flux:text>
                        </div>
                        <div class="space-y-6 mt-6">
                            <flux:input wire:model="form.username" label="{{ __('auth.username') }}" type="text" />
                            <flux:field>
                                <div class="mb-3 flex justify-between">
                                    <flux:label>
                                        {{ __('auth.password') }}
                                    </flux:label>

                                    <flux:link href="#" variant="subtle" class="text-sm">
                                        {{ __('auth.forgot_password') }}
                                    </flux:link>
                                </div>

                                <flux:input wire:model="form.password" type="password" />

                                <flux:error name="form.password" />
                            </flux:field>
                            <flux:field variant="inline">
                                <flux:checkbox wire:model="form.remember_me" />
                                <flux:label>{{ __('auth.remember_me') }}</flux:label>
                            </flux:field>
                        </div>
                        <div class="space-y-2 mt-6">
                            <flux:button type="submit" variant="primary" class="w-full">
                                {{ __('auth.login') }}
                            </flux:button>
                        </div>
                    </form>
                </div>
                <div class="hidden md:block md:w-1/2">
                    <img src="{{ asset('images/auth/' . $loginImage) }}" alt="Login Image"
                        class="h-full w-full object-cover" />
                </div>
            </div>
        </flux:card>
    </div>
</div>