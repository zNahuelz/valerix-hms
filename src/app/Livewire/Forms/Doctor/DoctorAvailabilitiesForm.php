<?php

namespace App\Livewire\Forms\Doctor;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Form;

class DoctorAvailabilitiesForm extends Form
{
    public array $availabilities = [];

    public function rules(): array
    {
        return [
            'availabilities' => ['required', 'array', 'min:1', 'max:7'],
            'availabilities.*.weekday' => ['required', 'integer', 'between:1,7'],
            'availabilities.*.start_time' => ['required', 'date_format:H:i'],
            'availabilities.*.end_time' => ['required', 'date_format:H:i'],
            'availabilities.*.break_start' => ['required', 'date_format:H:i'],
            'availabilities.*.break_end' => ['required', 'date_format:H:i'],
            'availabilities.*.is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
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

    public function validateAndCheck(): void
    {
        $this->validate();
        $this->validateAvailabilityTimes();
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

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    public function sanitized(): array
    {
        return [
            'availabilities' => $this->availabilities,
        ];
    }
}
