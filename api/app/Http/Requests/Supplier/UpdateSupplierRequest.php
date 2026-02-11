<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'manager' => ['required', 'string', 'min:2', 'max:50'],
            'ruc' => ['required', 'string', 'min:11', 'max:11', 'regex:/^\d{11}$/', Rule::unique('suppliers', 'ruc')->ignore($this->route('supplier'))],
            'address' => ['nullable',  'string', 'min:5', 'max:100'],
            'phone' => ['nullable',  'string', 'min:6', 'max:15', 'regex:/^\+?\d{6,15}$/'],
            'email' => ['nullable',  'email', 'max:50'],
            'description' => ['nullable', 'string', 'min:5', 'max:150'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => mb_strtoupper(trim($this->name)),
            'manager' => mb_strtoupper(trim($this->manager)),
            'address' => $this->address ? mb_strtoupper(trim($this->address)) : null,
            'phone' => $this->phone ? trim($this->phone) : null,
            'email' => $this->email ? mb_strtolower(trim($this->email)) : null,
            'description' => $this->description ? mb_strtoupper(trim($this->description)) : null,
        ]);
    }
}
