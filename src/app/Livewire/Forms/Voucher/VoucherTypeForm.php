<?php

namespace App\Livewire\Forms\Voucher;

use App\Models\VoucherType;
use Illuminate\Validation\Rule;
use Livewire\Form;

class VoucherTypeForm extends Form
{
    public ?VoucherType $voucherType = null;

    public $name = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:20', Rule::unique('voucher_types', 'name')
                ->ignore($this->voucherType?->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.voucherType.name.required'),
            'name.min' => __('validation.voucherType.name.min'),
            'name.max' => __('validation.voucherType.name.max'),
            'name.unique' => __('validation.voucherType.name.unique'),
        ];
    }

    public function sanitized(): array
    {
        return [
            'name' => strtoupper(trim($this->name)),
        ];
    }

    protected function prepareForValidation($attributes)
    {
        $attributes['name'] = strtoupper(trim($attributes['name'] ?? ''));

        return $attributes;
    }
}
