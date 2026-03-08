<?php

/** @noinspection DuplicatedCode */

namespace App\Livewire\Forms\Doctor;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Form;

class DoctorCreateForm extends Form
{
    public $names = '';

    public $paternal_surname = '';

    public $maternal_surname = '';

    public $dni = '';

    public $phone = '';

    public $address = '';

    public $hired_at = '2';

    public $clinic_id = '';

    public $email = '';

    public $role_id = '';

    public $availabilities = [];

    public function validateStep(int $step): void
    {
        $this->validate($this->rulesForStep($step));
        if ($step === 2) {
            $this->validateAvailabilityTimes();
        }
    }

    public function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
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
                    Rule::unique('doctors', 'dni'),
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
                    Rule::unique('users', 'email'),
                ],
            ],
            2 => [
                'availabilities' => ['required', 'array', 'min:5', 'max:7'],
                'availabilities.*.weekday' => ['required', 'integer', 'between:1,7'],
                'availabilities.*.start_time' => ['required', 'date_format:H:i'],
                'availabilities.*.end_time' => ['required', 'date_format:H:i'],
                'availabilities.*.break_start' => ['required', 'date_format:H:i'],
                'availabilities.*.break_end' => ['required', 'date_format:H:i'],
                'availabilities.*.is_active' => ['required', 'boolean'],
            ],
            default => [],
        };
    }

    private function validateAvailabilityTimes(): void
    {
        $activeCount = collect($this->availabilities)->where('is_active', true)->count();
        if ($activeCount === 0) {
            throw ValidationException::withMessages([
                'form.availabilities' => __('validation.availabilities.at_least_one_active'),
            ]);
        }

        $errors = [];
        foreach ($this->availabilities as $index => $slot) {
            try {
                $start = Carbon::createFromFormat('H:i', $slot['start_time'] ?? '');
                $end = Carbon::createFromFormat('H:i', $slot['end_time'] ?? '');
                $breakStart = Carbon::createFromFormat('H:i', $slot['break_start'] ?? '');
                $breakEnd = Carbon::createFromFormat('H:i', $slot['break_end'] ?? '');
            } catch (Exception) {
                // Format already caught by rulesForStep, skip cross-field checks
                continue;
            }

            if ($end->lessThanOrEqualTo($start)) {
                $errors["form.availabilities.{$index}.end_time"] =
                    __('validation.availabilities.end_time.less_than_start');
            } elseif ($start->diffInMinutes($end) > (15 * 60)) {
                $errors["form.availabilities.{$index}.end_time"] =
                    __('validation.availabilities.end_time.hour_diff');
            }

            if ($breakEnd->lessThanOrEqualTo($breakStart)) {
                $errors["form.availabilities.{$index}.break_end"] =
                    __('validation.availabilities.break_end.less_than_start');
            } elseif ($breakStart->diffInMinutes($breakEnd) > (2 * 60)) {
                $errors["form.availabilities.{$index}.break_end"] =
                    __('validation.availabilities.break_end.hour_diff');
            }

            if ($breakStart->lessThan($start) || $breakEnd->greaterThan($end)) {
                $errors["form.availabilities.{$index}.break_start"] =
                    __('validation.availabilities.break_start.less_than_start');
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
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
            'email' => strtolower(trim($this->email)),
            'clinic_id' => $this->clinic_id,
            'role_id' => $this->role_id,
            'availabilities' => $this->availabilities,
        ];
    }

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
            'hired_at.before_or_equal' => __('validation.hired_at.before_or_equal'),
            'email.required' => __('validation.email.required'),
            'email.max' => __('validation.email.max'),
            'email.email' => __('validation.email.email'),
            'email.unique' => __('validation.email.unique'),
            'clinic_id.required' => __('validation.clinic_id.required'),
            'clinic_id.exists' => __('validation.clinic_id.exists'),
            'role_id.required' => __('validation.role_id.required'),
            'role_id.exists' => __('validation.role_id.exists'),
            'availabilities.required' => __('validation.availabilities.required'),
            'availabilities.min' => __('validation.availabilities.min'),
            'availabilities.max' => __('validation.availabilities.max'),
            'availabilities.*.weekday.required' => __('validation.availabilities.weekday.required'),
            'availabilities.*.weekday.between' => __('validation.availabilities.weekday.between'),
            'availabilities.*.start_time.required' => __('validation.availabilities.start_time.required'),
            'availabilities.*.start_time.date_format' => __('validation.availabilities.start_time.date_format'),
            'availabilities.*.end_time.required' => __('validation.availabilities.end_time.required'),
            'availabilities.*.end_time.date_format' => __('validation.availabilities.end_time.date_format'),
            'availabilities.*.break_start.required' => __('validation.availabilities.break_start.required'),
            'availabilities.*.break_start.date_format' => __('validation.availabilities.break_start.date_format'),
            'availabilities.*.break_end.required' => __('validation.availabilities.break_end.required'),
            'availabilities.*.break_end.date_format' => __('validation.availabilities.break_end.date_format'),
            'availabilities.*.is_active.required' => __('validation.availabilities.is_active.required'),
            'availabilities.*.is_active.boolean' => __('validation.availabilities.is_active.boolean'),
        ];
    }
}
