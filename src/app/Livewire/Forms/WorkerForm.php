<?php

namespace App\Livewire\Forms;

use App\Models\Worker;
use Illuminate\Validation\Rule;
use Livewire\Form;

class WorkerForm extends Form
{
    public ?Worker $worker = null;

    public $names = '';

    public $paternal_surname = '';

    public $maternal_surname = '';

    public $dni = '';

    public $phone = '';

    public $address = '';

    public $hired_at = '';

    public $clinic_id = '';

    public $email = '';

    public $position = '';

    public $role_id = '';

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
            'dni.required' => __('validation.dni.required'),
            'dni.min' => __('validation.dni.size'),
            'dni.max' => __('validation.dni.size'),
            'dni.unique' => __('validation.dni.unique', ['entity' => strtolower(trans_choice('worker.worker', 1))]),
            'dni.regex' => __('validation.dni.regex'),
            'phone.required' => __('validation.phone.required'),
            'phone.min' => __('validation.phone.min'),
            'phone.max' => __('validation.phone.max'),
            'phone.regex' => __('validation.phone.regex'),
            'address.required' => __('validation.address.required'),
            'address.min' => __('validation.address.min'),
            'address.max' => __('validation.address.max'),
            'hired_at.required' => __('validation.hired_at.required'),
            'hired_at.date' => __('validation.hired_at.date'),
            'hired_at.before_or_equal' => __('validation.hired_at.before'),
            'position.required' => __('validation.position.required'),
            'position.in' => __('validation.position.in'),
            'email.required' => __('validation.email.required'),
            'email.max' => __('validation.email.max'),
            'email.email' => __('validation.email.email'),
            'email.unique' => __('validation.email.unique'),
            'clinic_id.required' => __('validation.clinic_id.required'),
            'clinic_id.exists' => __('validation.clinic_id.exists'),
            'role_id.required' => __('validation.role_id.required'),
            'role_id.exists' => __('validation.role_id.exists'),
        ];
    }

    public function sanitized(): array
    {
        return [
            'names' => strtoupper(trim($this->names)),
            'paternal_surname' => strtoupper(trim($this->paternal_surname)),
            'maternal_surname' => $this->maternal_surname ? strtoupper(trim($this->maternal_surname)) : null,
            'dni' => trim($this->dni),
            'phone' => trim($this->phone),
            'address' => strtoupper(trim($this->address)),
            'hired_at' => $this->hired_at,
            'position' => strtoupper(trim($this->position)),
            'email' => strtolower(trim($this->email)),
            'clinic_id' => $this->clinic_id,
            'role_id' => $this->role_id,
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
            'dni' => [
                'required',
                'string',
                'min:8',
                'max:15',
                'regex:/^[0-9]{8,15}$/',
                Rule::unique('workers', 'dni')
                    ->ignore($this->worker?->id),
            ],
            'phone' => [
                'required',
                'string',
                'min:6',
                'max:15',
                'regex:/^\+?\d{6,15}$/',
            ],
            'address' => [
                'required',
                'string',
                'min:5',
                'max:100',
            ],
            'hired_at' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'position' => [
                'required',
                'string',
                'in:GERENTE,ENCARGADO,SECRETARIA,VENDEDOR,SUPERVISOR,OTRO',
            ],
            'clinic_id' => [
                'required',
                Rule::exists('clinics', 'id'),
            ],
            'role_id' => [
                'required',
                Rule::exists('roles', 'id'),
            ],
            'email' => [
                'required',
                'email',
                'max:50',
                Rule::unique('users', 'email')
                    ->ignore($this->worker?->user()->withTrashed()->first()?->id),
            ],
        ];
    }
}
