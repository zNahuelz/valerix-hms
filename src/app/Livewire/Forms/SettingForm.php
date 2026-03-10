<?php

namespace App\Livewire\Forms;

use App\Enums\SettingType;
use App\Models\Setting;
use Illuminate\Validation\Rule;
use Livewire\Form;

class SettingForm extends Form
{
    public ?Setting $setting = null;

    public $key = '';

    public $value = '';

    public $value_type = '';

    public $description = '';

    protected function rules(): array
    {
        $valueRules = ['required'];

        $valueRules[] = match ($this->value_type) {
            SettingType::INTEGER->value => 'integer',
            SettingType::DOUBLE->value => 'numeric',
            SettingType::BOOLEAN->value => 'boolean',
            default => 'string',
        };

        return [
            'key' => ['required', 'string', Rule::unique('settings', '_key')->ignore($this->setting?->id)],
            'value' => $valueRules,
            'value_type' => ['required', Rule::enum(SettingType::class)],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'key.required' => __('validation.key.required'),
            'key.string' => __('validation.key.string'),
            'key.unique' => __('validation.key.unique'),
            'value.required' => __('validation.value.required'),
            'value.string' => __('validation.value.string'),
            'value.integer' => __('validation.value.numeric'),
            'value.numeric' => __('validation.value.double'),
            'value.boolean' => __('validation.value.boolean'),
            'value_type.required' => __('validation.value_type.required'),
            'value_type.enum' => __('validation.value_type.enum'),
            'description.nullable' => __('validation.description.nullable'),
            'description.string' => __('validation.description.string'),
        ];
    }

    public function sanitized(): array
    {
        $value = $this->value;

        if ($this->value_type === SettingType::BOOLEAN->value) {
            $value = (bool) $this->value;
        } elseif ($this->value_type === SettingType::INTEGER->value) {
            $value = (int) $this->value;
        } elseif ($this->value_type === SettingType::DOUBLE->value) {
            $value = (float) $this->value;
        }

        return [
            'key' => strtoupper(str_replace([' ','-'], '_', trim($this->key))),
            'value' => $value,
            'value_type' => $this->value_type,
            'description' => $this->description ?: null,
        ];
    }
}
