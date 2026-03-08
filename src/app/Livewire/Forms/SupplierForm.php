<?php

namespace App\Livewire\Forms;

use App\Models\Supplier;
use Illuminate\Validation\Rule;
use Livewire\Form;

class SupplierForm extends Form
{
    public ?Supplier $supplier = null;

    public $name = '';

    public $manager = '';

    public $ruc = '';

    public $address = '';

    public $phone = '';

    public $email = '';

    public $description = '';

    public function messages(): array
    {
        return [
            'name.required' => __('validation.name.required'),
            'name.min' => __('validation.name.min', ['min' => '2']),
            'name.max' => __('validation.name.max', ['max' => '150']),
            'description.required' => __('validation.description.required'),
            'description.min' => __('validation.description.min', ['min' => '5']),
            'description.max' => __('validation.description.max', ['max' => '150']),
            'manager.required' => __('validation.manager.required'),
            'manager.min' => __('validation.manager.min'),
            'manager.max' => __('validation.manager.max'),
            'ruc.required' => __('validation.ruc.required'),
            'ruc.size' => __('validation.ruc.size'),
            'ruc.regex' => __('validation.ruc.regex'),
            'ruc.unique' => __('validation.ruc.unique'),
            'address.min' => __('validation.address.min'),
            'address.max' => __('validation.address.max'),
            'phone.min' => __('validation.phone.min'),
            'phone.max' => __('validation.phone.max'),
            'phone.regex' => __('validation.phone.regex'),
            'email.max' => __('validation.email.max'),
            'email.email' => __('validation.email.email'),
        ];
    }

    public function sanitized(): array
    {
        return [
            'name' => strtoupper(trim($this->name)),
            'manager' => strtoupper(trim($this->manager)),
            'ruc' => trim($this->ruc),
            'address' => $this->address ? strtoupper(trim($this->address)) : null,
            'phone' => $this->phone ? trim($this->phone) : null,
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'description' => $this->description ? strtoupper(trim($this->description)) : null,
        ];
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:150',
            ],
            'manager' => [
                'required',
                'string',
                'min:2',
                'max:50',
            ],
            'ruc' => [
                'required',
                'string',
                'size:11',
                'regex:/^(10|20)\d{9}$/',
                Rule::unique('suppliers', 'ruc')
                    ->ignore($this->supplier?->id),
            ],
            'address' => [
                'nullable',
                'string',
                'min:5',
                'max:100',
            ],
            'phone' => [
                'nullable',
                'string',
                'min:6',
                'max:15',
                'regex:/^\+?\d{6,15}$/',
            ],
            'email' => [
                'nullable',
                'email',
                'max:50',
            ],
            'description' => [
                'nullable',
                'string',
                'min:5',
                'max:150',
            ],
        ];
    }
}
