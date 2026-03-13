<?php

namespace App\Livewire\Forms\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Form;

class SendPasswordRecoveryForm extends Form
{
    public $email = '';

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('validation.email.required'),
            'email.email' => __('validation.email.email'),
            'email.max' => __('validation.email.max'),
        ];
    }

    public function sendPasswordRecoveryEmail()
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_THROTTLED) {
            throw ValidationException::withMessages([
                'email' => __($status),
            ]);
        }

        return true;
    }
}
