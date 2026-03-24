<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Notifications\EmailUpdatedNotification;
use Illuminate\Validation\Rule;

new class extends Component {
    public string $new_email = '';
    public string $new_email_confirmation = '';
    public bool $isSubmitting = false;

    protected function rules(): array
    {
        return [
            'new_email' => ['required', 'email', 'max:50', 'confirmed', Rule::unique('users', 'email')->ignore(Auth::user()->id)],
        ];
    }

    protected function messages(): array
    {
        return [
            'new_email.required' => __('validation.new_email.required'),
            'new_email.max' => __('validation.new_email.max', ['max' => '50']),
            'new_email.confirmed' => __('validation.new_email.confirmed'),
        ];
    }

    public function submit(): void
    {
        $this->isSubmitting = true;
        $this->dispatch('lock-area');

        $this->validate();

        $user = Auth::user();

        try {
            $user->notify(new EmailUpdatedNotification(
                Carbon::now('America/Lima')->format('d/m/Y h:i:s A')
            ));
            $user->forceFill(['email' => $this->new_email])->save();
            $user->notify(new EmailUpdatedNotification(
                Carbon::now('America/Lima')->format('d/m/Y h:i:s A')
            ));
        } catch (Exception) {
            Session::flash('error', __('auth.errors.change_email_failed'));
            $this->redirectRoute('dashboard');
        }
        session()->invalidate();
        session()->regenerateToken();
        Auth::logout();
        Session::flash('success', __('auth.email_changed'));
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
                <form wire:submit.prevent="submit">
                    <div class="grid grid-cols-1  gap-2">
                        <flux:field>
                            <flux:label>{{ __('auth.new_email') }}</flux:label>
                            <flux:input
                                wire:model.blur="new_email"
                                type="email"
                                icon="envelope"
                            />
                            <flux:error name="new_email"/>
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('auth.confirm_new_email') }}</flux:label>
                            <flux:input
                                wire:model.blur="new_email_confirmation"
                                type="email"
                                icon="envelope"
                            />
                            <flux:error name="new_email_confirmation"/>
                        </flux:field>
                    </div>
                    <div class="flex justify-end mt-4">
                        <flux:button
                            type="submit"
                            wire:click="submit"
                            wire:loading.attr="disabled"
                            variant="primary"
                            icon="envelope"
                        >
                            <span wire:loading.remove wire:target="submit">{{ __('common.update') }}</span>
                            <span wire:loading wire:target="submit">{{ __('common.loading') }}</span>
                        </flux:button>
                    </div>
                </form>
            </flux:fieldset>
        </div>
    </div>
</div>

