<?php

namespace App\Livewire\Forms\Voucher;

use App\Models\VoucherSerie;
use App\Models\VoucherType;
use Illuminate\Validation\Rule;
use Livewire\Form;

class VoucherSerieForm extends Form
{
    public ?VoucherSerie $voucherSerie = null;

    public $voucher_type_id = '';

    public $serie = '';

    public $serie_number = '';

    public $next_value = '';

    public $is_active = false;

    public function messages(): array
    {
        return [
            'voucher_type_id.required' => __('validation.voucherSerie.voucher_type_id.required'),
            'voucher_type_id.exists' => __('validation.voucherSerie.voucher_type_id.exists'),
            'serie_number.required' => __('validation.voucherSerie.serie_number.required'),
            'serie_number.integer' => __('validation.voucherSerie.serie_number.integer'),
            'serie_number.min' => __('validation.voucherSerie.serie_number.min'),
            'serie_number.max' => __('validation.voucherSerie.serie_number.max'),
            'serie.required' => __('validation.voucherSerie.serie.required'),
            'serie.regex' => __('validation.voucherSerie.serie.regex'),
            'serie.unique' => __('validation.voucherSerie.serie.unique'),
            'next_value.numeric' => __('validation.voucherSerie.next_value.numeric'),
            'next_value.min' => __('validation.voucherSerie.next_value.min'),
            'next_value.max' => __('validation.voucherSerie.next_value.max'),
            'is_active.boolean' => __('validation.voucherSerie.is_active.boolean'),
        ];
    }

    public function sanitized(): array
    {
        return [
            'voucher_type_id' => $this->voucher_type_id,
            'serie' => $this->computedSerie(),
            'next_value' => $this->next_value,
            'is_active' => $this->is_active,
        ];
    }

    public function computedSerie(): string
    {
        if (! $this->voucher_type_id || ! $this->serie_number) {
            return '';
        }

        $type = VoucherType::find($this->voucher_type_id);
        $letter = $type ? strtoupper(substr($type->name, 0, 1)) : '';
        $number = str_pad((int) $this->serie_number, 3, '0', STR_PAD_LEFT);

        return "{$letter}{$number}";
    }

    protected function rules(): array
    {
        return [
            'voucher_type_id' => ['required', Rule::exists('voucher_types', 'id')],
            'serie_number' => ['required', 'integer', 'min:1', 'max:999'],
            'serie' => ['required', Rule::unique('voucher_series', 'serie')
                ->ignore($this->voucherSerie?->id)],
            'next_value' => ['numeric', 'min:1', 'max:999999999'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation($attributes)
    {
        $attributes['serie'] = $this->computedSerie();

        return $attributes;
    }
}
