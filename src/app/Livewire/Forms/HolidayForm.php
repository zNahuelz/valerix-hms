<?php

namespace App\Livewire\Forms;

use App\Models\Holiday;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Form;

class HolidayForm extends Form
{
    public ?Holiday $holiday = null;

    public $name = '';

    public $date = '';

    public $is_recurring = false;

    public function messages(): array
    {
        return [
            'name.required' => __('validation.name.required'),
            'name.min' => __('validation.name.min', ['min' => '5']),
            'name.max' => __('validation.name.max', ['max' => '100']),
            'name.unique' => __('validation.name.unique_holiday'),
            'date.required' => __('validation.date.required'),
            'date.date' => __('validation.date.date'),
            'date.unique' => __('validation.date.unique_holiday'),
        ];
    }

    public function sanitized(): array
    {
        return [
            'name' => strtoupper(trim($this->name)),
            'date' => $this->date,
            'is_recurring' => (bool) $this->is_recurring,
        ];
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:5',
                'max:100',
                Rule::unique('holidays', 'name')->ignore($this->holiday?->id),
            ],
            'date' => [
                'required',
                'date',
                Rule::unique('holidays', 'date')->ignore($this->holiday?->id),
                function ($attribute, $value, $fail) {
                    // if (!$this->is_recurring) return;
                    $monthDay = Carbon::parse($value)->format('m-d');
                    $exists = Holiday::where('is_recurring', true)
                        ->whereRaw("TO_CHAR(date, 'MM-DD') = ?", [$monthDay])
                        ->when($this->holiday?->id, fn ($q) => $q->where('id', '!=', $this->holiday->id))
                        ->exists();

                    if ($exists) {
                        $fail(__('validation.date.recurring_date_taken'));
                    }
                },
            ],
        ];
    }
}
