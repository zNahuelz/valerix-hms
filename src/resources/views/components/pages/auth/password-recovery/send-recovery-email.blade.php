<?php

use Livewire\Component;
use App\Livewire\Forms\Auth\SendPasswordRecoveryForm;

new class extends Component
{
    public SendPasswordRecoveryForm $form;

    public bool $sent = false;

    public function send(): void
    {
        $this->sent = $this->form->sendPasswordRecoveryEmail();
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

                    @if ($sent)
                        <div class="space-y-4 text-center">
                            <flux:heading size="xl" class="font-bold!">
                                {{ __('auth.check_your_email') }}
                            </flux:heading>
                            <flux:text>
                                {{ __('auth.recovery_email_sent') }}
                            </flux:text>
                        </div>
                    @else
                        <form wire:submit.prevent="send">
                            <div>
                                <flux:heading size="xl" class="font-bold! text-center">
                                    {{ __('auth.forgot_password') }}
                                </flux:heading>
                                <flux:text class="mt-2 text-center">
                                    {{ __('auth.recovery_instructions') }}
                                </flux:text>
                            </div>

                            <div class="space-y-6 mt-6">
                                <flux:input
                                    wire:model="form.email"
                                    label="{{ __('common.email') }}"
                                    type="email"
                                    wire:loading.attr="disabled"
                                    wire:target="send"
                                    placeholder="{{__('common.email_placeholder')}}"
                                />
                            </div>

                            <div class="space-y-2 mt-6">
                                <flux:button
                                    type="submit"
                                    variant="primary"
                                    class="w-full"
                                    wire:loading.attr="disabled"
                                    wire:target="send"
                                >
                                    {{ __('common.continue') }}
                                </flux:button>

                                <flux:button href="{{ route('login') }}" variant="ghost" class="w-full" wire:loading.attr="disabled">
                                    {{ __('common.back') }}
                                </flux:button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </flux:card>
    </div>
</div>
