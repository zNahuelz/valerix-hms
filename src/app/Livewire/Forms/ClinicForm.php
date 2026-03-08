<?php

namespace App\Livewire\Forms;

use App\Models\Clinic;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ClinicForm extends Form
{
    public ?Clinic $clinic = null;

    public string $name = '';

    public string $ruc = '';

    public string $address = '';

    public string $phone = '';

    public function messages(): array
    {
        return [
            'name.required' => __('validation.name.required'),
            'name.min' => __('validation.name.min', ['min' => '2']),
            'name.max' => __('validation.name.max', ['max' => '100']),
            'name.unique' => __('validation.name.unique_clinic'),
            'ruc.required' => __('validation.ruc.required'),
            'ruc.size' => __('validation.ruc.size'),
            'ruc.regex' => __('validation.ruc.regex'),
            'ruc.unique' => __('validation.ruc.unique'),
            'address.required' => __('validation.address.required'),
            'address.min' => __('validation.address.min'),
            'address.max' => __('validation.address.max'),
            'phone.required' => __('validation.phone.required'),
            'phone.min' => __('validation.phone.min'),
            'phone.max' => __('validation.phone.max'),
            'phone.regex' => __('validation.phone.regex'),
        ];
    }

    public function sanitized(): array
    {
        return [
            'name' => strtoupper(trim($this->name)),
            'ruc' => trim($this->ruc),
            'address' => $this->address ? strtoupper(trim($this->address)) : null,
            'phone' => $this->phone ? trim($this->phone) : null,
        ];
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                Rule::unique('clinics', 'name')->where(fn ($query) => $query->whereRaw('LOWER(name) = LOWER(?)', [$this->name]))
                    ->ignore($this->clinic?->id),
            ],
            'ruc' => [
                'required',
                'string',
                'size:11',
                'regex:/^(10|20)\d{9}$/',
            ],
            'address' => [
                'required',
                'string',
                'min:5',
                'max:150',
            ],
            'phone' => [
                'required',
                'string',
                'min:6',
                'max:15',
                'regex:/^\+?\d{6,15}$/',
            ],
        ];
    }
}
