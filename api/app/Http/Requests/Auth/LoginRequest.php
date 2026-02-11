<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:5', 'max:20'],
            'password' => ['required', 'string', 'min:5', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'auth.validation.username.required',
            'username.min' => 'auth.validation.username.min',
            'password.required' => 'auth.validation.password.required',
            'password.min' => 'auth.validation.password.min',
        ];
    }
}
