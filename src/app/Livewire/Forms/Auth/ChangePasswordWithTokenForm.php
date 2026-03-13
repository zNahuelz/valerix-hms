<?php

namespace App\Livewire\Forms\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Form;

class ChangePasswordWithTokenForm extends Form
{
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:50'],
            'token' => ['required'],
            'password' => ['required', 'string', 'min:5', 'max:20', 'confirmed'],
            'password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('validation.email.required'),
            'email.email' => __('validation.email.email'),
            'email.max' => __('validation.email.max'),
            'password.required' => __('validation.new_password.required'),
            'password.min' => __('validation.new_password.min'),
            'password.max' => __('validation.new_password.max'),
            'password_confirmation.required' => __('validation.new_password.confirmed'),
            'password.confirmed' => __('validation.new_password.confirmed'),
        ];
    }

    public function resetPassword(): User
    {
        $this->validate();

        $user = null;

        $status = Password::reset(
            [
                'email' => $this->email,
                'token' => $this->token,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ],
            function (User $resolvedUser, string $password) use (&$user) {
                $resolvedUser->forceFill(['password' => $password])->save();
                $user = $resolvedUser;
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'token' => __($status),
            ]);
        }

        return $user;
    }
}
