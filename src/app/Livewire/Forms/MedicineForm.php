<?php

namespace App\Livewire\Forms;

use App\Models\Medicine;
use Illuminate\Validation\Rule;
use Livewire\Form;

class MedicineForm extends Form
{
    public ?Medicine $medicine = null;

    public string $name = '';

    public string $composition = '';

    public string $description = '';

    public string $barcode = '';

    public string $presentation_id = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:5', 'max:100'],
            'composition' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['nullable', 'string', 'min:5', 'max:255'],
            'barcode' => ['required', 'string', 'min:8', 'max:30', 'regex:/^[A-Za-z0-9]{8,30}$/', Rule::unique('medicines', 'barcode')->ignore($this->medicine?->id)],
            'presentation_id' => ['required', Rule::exists('presentations', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.name.required'),
            'name.min' => __('validation.name.min', ['min' => '5']),
            'name.max' => __('validation.name.max', ['max' => '100']),
            'composition.required' => __('validation.composition.required'),
            'composition.min' => __('validation.composition.min', ['min' => '5']),
            'composition.max' => __('validation.composition.max', ['max' => '255']),
            'description.required' => __('validation.description.required'),
            'description.min' => __('validation.description.min', ['min' => '5']),
            'description.max' => __('validation.description.max', ['max' => '255']),
            'barcode.required' => __('validation.barcode.required'),
            'barcode.min' => __('validation.barcode.min', ['min' => '8']),
            'barcode.max' => __('validation.barcode.max', ['max' => '30']),
            'barcode.regex' => __('validation.barcode.regex'),
            'barcode.unique' => __('validation.barcode.unique'),
        ];
    }

    public function sanitized(): array
    {
        return [
            'name' => strtoupper(trim($this->name)),
            'composition' => strtoupper(trim($this->composition)),
            'description' => strtoupper(trim($this->description)),
            'barcode' => strtoupper(trim($this->barcode)),
            'presentation_id' => $this->presentation_id,
        ];
    }
}
