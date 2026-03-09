<?php

namespace App\Livewire\Forms\Doctor;

use App\Enums\UnavailabilityReason;
use App\Models\DoctorUnavailability;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Form;

class DoctorUnavailabilitiesForm extends Form
{
    public ?DoctorUnavailability $doctorUnavailability = null;

    public $doctor_id = '';

    public $start_datetime = '';

    public $end_datetime = '';

    public $reason = '';

    public function messages(): array
    {
        return [
            'doctor_id.required' => __('validation.unavailabilities.doctor_id.required'),
            'doctor_id.exists' => __('validation.unavailabilities.doctor_id.exists'),
            'start_datetime.required' => __('validation.unavailabilities.start_datetime.required'),
            'start_datetime.date' => __('validation.unavailabilities.start_datetime.date'),
            'end_datetime.required' => __('validation.unavailabilities.end_datetime.required'),
            'end_datetime.date' => __('validation.unavailabilities.end_datetime.date'),
            'end_datetime.after' => __('validation.unavailabilities.end_datetime.after'),
            'end_datetime.min_gap' => __('validation.unavailabilities.end_datetime.min_gap'),
            'reason.required' => __('validation.unavailabilities.reason.required'),
            'reason.exists' => __('validation.unavailabilities.reason.enum'),
        ];
    }

    protected function rules(): array
    {
        return [
            'doctor_id' => ['required', Rule::exists('doctors', 'id')],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['required', 'date', 'after:start_datetime',
                function ($attribute, $value, $fail) {
                    try {
                        $start = Carbon::createFromFormat('Y-m-d\TH:i', $this->start_datetime);
                        $end = Carbon::createFromFormat('Y-m-d\TH:i', $value);
                    } catch (\Exception $e) {
                        return;
                    }
                    if ($start->diffInHours($end) < 24) {
                        $fail(__('validation.unavailabilities.end_datetime.min_gap'));
                    }
                },],
            'reason' => ['required', Rule::enum(UnavailabilityReason::class)],
        ];
    }
}
