<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $user = null;

    public $email = '';

    public $username = '';

    public function messages(): array
    {
        return [
            'email.required' => __('validation.email.required'),
            'email.email' => __('validation.email.email'),
            'email.max' => __('validation.email.max'),
            'email.unique' => __('validation.email.unique'),
            'username.required' => __('validation.username.required'),
            'username.min' => __('validation.username.min', ['min' => '5']),
            'username.max' => __('validation.username.max', ['max' => '20']),
            'username.unique' => __('validation.username.unique'),
        ];
    }

    public function sanitized(): array
    {
        return [
            'email' => strtolower(trim($this->email)),
            'username' => trim($this->username),
        ];
    }

    protected function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:50',
                Rule::unique('users', 'email')
                    ->ignore($this->user?->id),
            ],
            'username' => [
                'required',
                'string',
                'min:5',
                'max:20',
                Rule::unique('users', 'username')
                    ->ignore($this->user?->id),
            ],
        ];
    }
}
