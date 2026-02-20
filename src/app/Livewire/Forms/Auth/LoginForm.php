<?php

namespace App\Livewire\Forms\Auth;

use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate(['required', 'min:5', 'max:20'])]
    public $username = '';

    #[Validate(['required', 'min:5', 'max:20'])]
    public $password = '';

    public $remember_me = false;

    public function messages(): array
    {
        return [
            'username.required' => __('auth.validation.username.required'),
            'username.min' => __('auth.validation.username.max'),
            'username.max' => __('auth.validation.username.max'),
            'password.required' => __('auth.validation.password.required'),
            'password.min' => __('auth.validation.password.max'),
            'password.max' => __('auth.validation.password.max'),
        ];
    }
}
