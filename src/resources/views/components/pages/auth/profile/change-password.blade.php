<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';
    public int $failedAttempts = 0;
    public bool $isSubmitting = false;

    protected function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'min:5', 'max:20'],
            'new_password' => ['required', 'string', 'min:5', 'max:20', 'confirmed'],
        ];
    }

    protected function messages(): array
    {
        return [
            'current_password.required' => __('validation.current_password.required'),
            'current_password.min' => __('validation.current_password.min', ['min' => '5']),
            'current_password.max' => __('validation.current_password.max', ['max' => '20']),
            'new_password.required' => __('validation.new_password.required'),
            'new_password.min' => __('validation.new_password.min', ['min' => '5']),
            'new_password.max' => __('validation.new_password.max', ['max' => '20']),
            'new_password.confirmed' => __('validation.new_password.confirmed'),
        ];
    }

    public function submit(): void
    {
        if ($this->failedAttempts >= 3) {
            $this->forceLogout();
            return;
        }

        $this->isSubmitting = true;
        $this->dispatch('lock-area');

        $this->validate();

        $user = Auth::user();

        try {
            if (!Hash::check($this->current_password, $user->password)) {
                $this->failedAttempts++;

                if ($this->failedAttempts >= 3) {
                    $this->forceLogout();
                    return;
                }

                $this->isSubmitting = false;
                $this->dispatch('unlock-area');
                $this->addError('current_password', __('validation.current_password.incorrect', ['try' => "$this->failedAttempts/3"]));
                return;
            }

            $user->forceFill(['password' => Hash::make($this->new_password)])->save();
        } catch (Exception) {
            Session::flash('error', __('auth.errors.change_password_failed'));
            $this->redirectRoute('dashboard');
        }
        Auth::logoutOtherDevices($this->current_password);

        session()->invalidate();
        session()->regenerateToken();
        Auth::logout();

        $this->redirectRoute('login');
    }

    private function forceLogout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->redirectRoute('login');
    }
};
?>

<div>
    <div class="flex justify-center px-4">
        <div class="w-md mt-2">
            <flux:fieldset>
                @if($failedAttempts > 0)
                    <x-shared.alert
                        type="error">{{__('auth.failed_attempts', ['count' => 3 - $failedAttempts ])}}</x-shared.alert>
                @endif
                <div class="grid grid-cols-1  gap-2">
                    <flux:field>
                        <flux:label>{{ __('auth.current_password') }}</flux:label>
                        <flux:input
                            wire:model.blur="current_password"
                            type="password"
                            icon="lock-closed"
                            viewable
                        />
                        <flux:error name="current_password"/>
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('auth.new_password') }}</flux:label>
                        <flux:input
                            wire:model.blur="new_password"
                            type="password"
                            icon="lock-closed"
                            viewable
                        />
                        <flux:error name="new_password"/>
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('auth.confirm_new_password') }}</flux:label>
                        <flux:input
                            wire:model.blur="new_password_confirmation"
                            type="password"
                            icon="lock-closed"
                            viewable
                        />
                        <flux:error name="new_password_confirmation"/>
                    </flux:field>
                </div>
                <div class="flex justify-end mt-4">
                    <flux:button
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        variant="primary"
                        icon="lock-closed"
                    >
                        <span wire:loading.remove wire:target="submit">{{ __('auth.change_password') }}</span>
                        <span wire:loading wire:target="submit">{{ __('common.loading') }}</span>
                    </flux:button>
                </div>
            </flux:fieldset>
        </div>
    </div>
</div>
