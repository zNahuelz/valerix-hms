<?php

namespace App\Livewire\Forms;

use App\Enums\PaymentAction;
use App\Models\PaymentType;
use Illuminate\Validation\Rule;
use Livewire\Form;

class PaymentTypeForm extends Form
{
    public ?PaymentType $paymentType = null;

    public $name = '';

    public $action = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:50', Rule::unique('payment_types', 'name')
                ->ignore($this->paymentType?->id)],
            'action' => ['required', Rule::enum(PaymentAction::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.paymentType.name.required'),
            'name.min' => __('validation.paymentType.name.min'),
            'name.max' => __('validation.paymentType.name.max'),
            'name.unique' => __('validation.paymentType.name.unique'),
            'action.required' => __('validation.paymentType.action.required'),
            'action.enum' => __('validation.paymentType.action.enum'),
        ];
    }

    public function sanitized(): array
    {
        return [
            'name' => strtoupper(trim($this->name)),
            'action' => strtoupper(trim($this->action)),
        ];
    }

    protected function prepareForValidation($attributes)
    {
        $attributes['name'] = strtoupper(trim($attributes['name'] ?? ''));

        return $attributes;
    }
}
