<?php

namespace App\Livewire\Forms;

use App\Models\Presentation;
use Illuminate\Validation\Rule;
use Livewire\Form;

class PresentationForm extends Form
{
    public ?Presentation $presentation = null;

    public $name = '';

    public $description = '';

    public $numeric_value = '';

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:50',
            ],
            'description' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],
            'numeric_value' => [
                'required',
                'numeric',
                'min:0.1',
                'max:99999',
                Rule::unique('presentations', 'numeric_value')
                    ->where(fn ($query) => $query->whereRaw('LOWER(name) = ?', [strtolower($this->name)]))
                    ->ignore($this->presentation?->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.name.required'),
            'name.min' => __('validation.name.min', ['min' => '2']),
            'name.max' => __('validation.name.max', ['max' => '50']),
            'description.required' => __('validation.name.required'),
            'description.min' => __('validation.description.min', ['min' => '2']),
            'description.max' => __('validation.description.max', ['max' => '100']),
            'numeric_value.required' => __('validation.numeric_value.required'),
            'numeric_value.numeric' => __('validation.numeric_value.numeric'),
            'numeric_value.min' => __('validation.numeric_value.min'),
            'numeric_value.max' => __('validation.numeric_value.max'),
            'numeric_value.unique' => __('validation.numeric_value.unique'),
        ];
    }

    public function sanitized(): array
    {
        return [
            'name' => strtoupper(trim($this->name)),
            'description' => strtoupper(trim($this->description)),
            'numeric_value' => $this->numeric_value,
        ];
    }
}
