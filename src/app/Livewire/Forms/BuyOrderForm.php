<?php

namespace App\Livewire\Forms;

use App\Enums\BuyOrderStatus;
use App\Models\BuyOrder;
use Illuminate\Validation\Rule;
use Livewire\Form;

class BuyOrderForm extends Form
{
    public ?BuyOrder $buyOrder = null;

    public $clinic_id = '';

    public $supplier_id = '';

    public $tax = '';

    public $subtotal = '';

    public $total = '';

    public $status = '';

    public array $buy_order_details = [];

    public function messages(): array
    {
        return [
            'clinic_id.required' => __('validation.clinic_id.required'),
            'clinic_id.exists' => __('validation.clinic_id.exists'),
            'supplier_id.required' => __('validation.supplier_id.required'),
            'supplier_id.exists' => __('validation.supplier_id.exists'),
            'tax.required' => __('validation.tax.required'),
            'tax.numeric' => __('validation.tax.numeric'),
            'tax.min' => __('validation.tax.min', ['min' => '0']),
            'tax.max' => __('validation.tax.max', ['max' => '99999999']),
            'subtotal.required' => __('validation.subtotal.required'),
            'subtotal.numeric' => __('validation.subtotal.numeric'),
            'subtotal.min' => __('validation.subtotal.min', ['min' => '0']),
            'subtotal.max' => __('validation.subtotal.max', ['max' => '99999999']),
            'total.required' => __('validation.total.required'),
            'total.numeric' => __('validation.total.numeric'),
            'total.min' => __('validation.total.min', ['min' => '0']),
            'total.max' => __('validation.total.max', ['max' => '99999999']),
            'status.required' => __('validation.buy_order_status.required'),
            'status.enum' => __('validation.buy_order_status.enum'),
            'buy_order_details.required' => __('validation.buy_order_detail.required'),
            'buy_order_details.array' => __('validation.buy_order_detail.array'),
            'buy_order_details.*.medicine_id.required' => __('validation.medicine_id.required'),
            'buy_order_details.*.medicine_id.exists' => __('validation.medicine_id.exists'),
            'buy_order_details.*.amount.required' => __('validation.amount.required'),
            'buy_order_details.*.amount.numeric' => __('validation.amount.numeric'),
            'buy_order_details.*.amount.min' => __('validation.amount.min', ['min' => '1']),
            'buy_order_details.*.amount.max' => __('validation.amount.max', ['max' => '99999999']),
            'buy_order_details.*.unit_price.required' => __('validation.unit_price.required'),
            'buy_order_details.*.unit_price.numeric' => __('validation.unit_price.numeric'),
            'buy_order_details.*.unit_price.min' => __('validation.unit_price.min', ['min' => '0.1']),
            'buy_order_details.*.unit_price.max' => __('validation.unit_price.max', ['max' => '99999999']),
        ];
    }

    public function sanitized(): array
    {
        return [
            'clinic_id' => $this->clinic_id,
            'supplier_id' => $this->supplier_id,
            'tax' => $this->tax,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'status' => $this->status,
            'buy_order_details' => $this->buy_order_details,
        ];
    }

    protected function rules(): array
    {
        return [
            'clinic_id' => ['required', Rule::exists('clinics', 'id')],
            'supplier_id' => ['required', Rule::exists('suppliers', 'id')],
            'tax' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'subtotal' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'total' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'status' => ['required', Rule::enum(BuyOrderStatus::class)],
            'buy_order_details' => ['required', 'array', 'min:1'],
            'buy_order_details.*.medicine_id' => ['required', Rule::exists('medicines', 'id')],
            'buy_order_details.*.amount' => ['required', 'numeric', 'min:1', 'max:99999'],
            'buy_order_details.*.unit_price' => ['required', 'numeric', 'min:0.1', 'max:99999999'],
        ];
    }
}
