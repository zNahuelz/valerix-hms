<?php


use App\Livewire\Forms\Auth\ChangePasswordWithTokenForm;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

new class extends Component
{
    public ChangePasswordWithTokenForm $form;

    public function mount(string $token, string $email): void
    {
        $this->form->token = $token;
        $this->form->email = $email;
    }

    public function save(): void
    {
        $user = $this->form->resetPassword();
        Auth::login($user);
        Session::flash('success',__('auth.password_recovered', ['user' => $user->username]));
        $this->redirectRoute('dashboard');
    }
};
?>

<div class="flex min-h-svh flex-col items-center justify-center p-6 ">
    <div class="w-full max-w-md">
        <flux:card class="overflow-hidden p-0">
            <div class="p-6 w-full space-y-6">
                <div class="p-6  space-y-6">
                    <div class="flex flex-col items-center">
                        <img src="{{ asset('images/app-icon.png') }}" class="max-w-12.5" alt="App Icon">
                    </div>

                    <form wire:submit.prevent="save">
                        <div>
                            <flux:heading size="xl" class="font-bold! text-center">
                                {{ __('auth.reset_password') }}
                            </flux:heading>
                            <flux:text class="mt-2 text-center">
                                {{ __('auth.reset_password_instructions') }}
                            </flux:text>
                        </div>

                        <div class="space-y-6 mt-6">
                            <flux:input
                                wire:model="form.email"
                                label="{{ __('common.email') }}"
                                type="email"
                                readonly
                            />

                            <flux:input
                                wire:model="form.password"
                                label="{{ __('auth.new_password') }}"
                                type="password"
                                wire:loading.attr="disabled"
                                wire:target="save"
                            />

                            <flux:input
                                wire:model="form.password_confirmation"
                                label="{{ __('auth.confirm_new_password') }}"
                                type="password"
                                wire:loading.attr="disabled"
                                wire:target="save"
                            />
                            <flux:error name="form.token" />
                        </div>

                        <div class="mt-6">
                            <flux:button
                                type="submit"
                                variant="primary"
                                class="w-full"
                                wire:loading.attr="disabled"
                                wire:target="save"
                            >
                                {{ __('common.continue') }}
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </flux:card>
    </div>
</div>
