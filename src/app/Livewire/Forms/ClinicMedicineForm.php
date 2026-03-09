<?php

namespace App\Livewire\Forms;

use App\Models\ClinicMedicine;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ClinicMedicineForm extends Form
{
    public ?ClinicMedicine $clinicMedicine = null;

    public $clinic_id = '';

    public $medicine_id = '';

    public $buy_price = '';

    public $sell_price = '';

    public $tax = '';

    public $profit = '';

    public $stock = '';

    public $salable = false;

    public function messages(): array
    {
        return [
            'clinic_id.required' => __('validation.clinic_id.required'),
            'clinic_id.exists' => __('validation.clinic_id.exists'),
            'medicine_id.required' => __('validation.medicine_id.required'),
            'medicine_id.exists' => __('validation.medicine_id.exists'),
            'buy_price.required' => __('validation.buy_price.required'),
            'buy_price.numeric' => __('validation.buy_price.numeric'),
            'buy_price.min' => __('validation.buy_price.min', ['min' => '0.1']),
            'buy_price.max' => __('validation.buy_price.max', ['max' => '999999999']),
            'sell_price.required' => __('validation.sell_price.required'),
            'sell_price.numeric' => __('validation.sell_price.numeric'),
            'sell_price.min' => __('validation.sell_price.min', ['min' => '0.1']),
            'sell_price.max' => __('validation.sell_price.max', ['max' => '999999999']),
            'sell_price.gte' => __('validation.sell_price.gte'),
            'tax.required' => __('validation.tax.required'),
            'tax.numeric' => __('validation.tax.numeric'),
            'tax.min' => __('validation.tax.min', ['min' => '0']),
            'tax.max' => __('validation.tax.max', ['max' => '999999999']),
            'profit.required' => __('validation.profit.required'),
            'profit.numeric' => __('validation.profit.numeric'),
            'profit.min' => __('validation.profit.min', ['min' => '0']),
            'profit.max' => __('validation.profit.max', ['max' => '999999999']),
            'stock.required' => __('validation.stock.required'),
            'stock.numeric' => __('validation.stock.numeric'),
            'stock.min' => __('validation.stock.min', ['min' => '0']),
            'stock.max' => __('validation.stock.max', ['max' => '999999999']),
            'salable.required' => __('validation.salable.required'),
            'salable.boolean' => __('validation.salable.boolean'),
        ];
    }

    public function sanitized(): array
    {
        return [
            'clinic_id' => $this->clinic_id,
            'medicine_id' => $this->medicine_id,
            'buy_price' => $this->buy_price,
            'sell_price' => $this->sell_price,
            'tax' => $this->tax,
            'profit' => $this->profit,
            'stock' => $this->stock,
            'salable' => $this->salable,
        ];
    }

    protected function rules(): array
    {
        return [
            'clinic_id' => ['required', Rule::exists('clinics', 'id')],
            'medicine_id' => ['required', Rule::exists('medicines', 'id')],
            'buy_price' => ['required', 'numeric', 'min:0.01', 'max:999999999'],
            'sell_price' => ['required', 'numeric', 'min:0.01', 'max:999999999', 'gte:buy_price'],
            'tax' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'profit' => ['required', 'numeric', 'max:999999999'],
            'stock' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'salable' => ['required', 'boolean'],
        ];
    }
}
