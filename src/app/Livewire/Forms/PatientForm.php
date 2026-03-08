<?php

namespace App\Livewire\Forms;

use App\Models\Patient;
use Illuminate\Validation\Rule;
use Livewire\Form;

class PatientForm extends Form
{
    public ?Patient $patient = null;

    public $names = '';

    public $paternal_surname = '';

    public $maternal_surname = '';

    public $birth_date = '';

    public $dni = '';

    public $email = '';

    public $phone = '';

    public $address = '';

    public function messages(): array
    {
        return [
            'names.required' => __('validation.name.required'),
            'names.min' => __('validation.name.min', ['min' => '2']),
            'names.max' => __('validation.name.max', ['max' => '30']),
            'paternal_surname.required' => __('validation.paternal_surname.required'),
            'paternal_surname.min' => __('validation.paternal_surname.min', ['min' => '2']),
            'paternal_surname.max' => __('validation.paternal_surname.max', ['max' => '30']),
            'maternal_surname.required' => __('validation.maternal_surname.required'),
            'maternal_surname.min' => __('validation.maternal_surname.min', ['min' => '2']),
            'maternal_surname.max' => __('validation.maternal_surname.max', ['max' => '30']),
            'birth_date.required' => __('validation.birth_date.required'),
            'birth_date.date' => __('validation.birth_date.date'),
            'birth_date.before' => __('validation.birth_date.before'),
            'dni.required' => __('validation.dni.required'),
            'dni.min' => __('validation.dni.size'),
            'dni.max' => __('validation.dni.size'),
            'dni.unique' => __('validation.dni.unique', ['entity' => strtolower(trans_choice('patient.patient', 1))]),
            'dni.regex' => __('validation.dni.regex'),
            'email.max' => __('validation.email.max'),
            'email.email' => __('validation.email.email'),
            'phone.min' => __('validation.phone.min'),
            'phone.max' => __('validation.phone.max'),
            'phone.regex' => __('validation.phone.regex'),
            'address.min' => __('validation.address.min'),
            'address.max' => __('validation.address.max'),
        ];
    }

    public function sanitized(): array
    {
        return [
            'names' => strtoupper(trim($this->names)),
            'paternal_surname' => strtoupper(trim($this->paternal_surname)),
            'maternal_surname' => $this->maternal_surname ? strtoupper(trim($this->maternal_surname)) : null,
            'birth_date' => $this->birth_date,
            'dni' => trim($this->dni),
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'phone' => $this->phone ? trim($this->phone) : null,
            'address' => $this->address ? strtoupper(trim($this->address)) : null,
        ];
    }

    protected function rules(): array
    {
        return [
            'names' => [
                'required',
                'string',
                'min:2',
                'max:30',
            ],
            'paternal_surname' => [
                'required',
                'string',
                'min:2',
                'max:30',
            ],
            'maternal_surname' => [
                'nullable',
                'string',
                'min:2',
                'max:30',
            ],
            'birth_date' => [
                'required',
                'date',
                'before:today',
            ],
            'dni' => [
                'required',
                'string',
                'min:8',
                'max:15',
                'regex:/^[0-9]{8,15}$/',
                Rule::unique('patients', 'dni')
                    ->ignore($this->patient?->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:50',
            ],
            'phone' => [
                'nullable',
                'string',
                'min:6',
                'max:15',
                'regex:/^\+?\d{6,15}$/',
            ],
            'address' => [
                'nullable',
                'string',
                'min:5',
                'max:100',
            ],
        ];
    }
}
