<?php

namespace App\Livewire\Forms;

use App\Models\Treatment;
use Illuminate\Validation\Rule;
use Livewire\Form;

class TreatmentForm extends Form
{
    public ?Treatment $treatment = null;

    public $name = '';

    public $description = '';

    public $procedure = '';

    public $price = '';

    public $tax = '';

    public $profit = '';

    public array $medicines = [];

    public function validateStep(int $step): void
    {
        $rules = $step === 1 ? $this->stepOneRules() : $this->stepTwoRules();
        $messages = $this->messages();

        $relevantMessages = array_filter(
            $messages,
            fn ($key) => collect(array_keys($rules))
                ->contains(fn ($rule) => str_starts_with($key, str_replace('.*', '', $rule))),
            ARRAY_FILTER_USE_KEY
        );

        $this->validate($rules, $relevantMessages);
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.name.required'),
            'name.min' => __('validation.name.min', ['min' => '5']),
            'name.max' => __('validation.name.max', ['max' => '100']),
            'name.unique' => __('validation.name.unique_treatment'),
            'description.min' => __('validation.description.min', ['min' => '5']),
            'description.max' => __('validation.description.max', ['max' => '255']),
            'procedure.min' => __('validation.procedure.min', ['min' => '5']),
            'procedure.max' => __('validation.procedure.max', ['max' => '255']),
            'price.required' => __('validation.price.required'),
            'price.numeric' => __('validation.price.numeric'),
            'price.min' => __('validation.price.min', ['min' => '1']),
            'price.max' => __('validation.price.max', ['max' => '999.999']),
            'tax.numeric' => __('validation.tax.numeric'),
            'tax.min' => __('validation.tax.min', ['min' => '0']),
            'tax.max' => __('validation.tax.max', ['max' => '999999']),
            'profit.required' => __('validation.profit.required'),
            'profit.numeric' => __('validation.profit.numeric'),
            'profit.min' => __('validation.profit.min', ['min' => '0']),
            'profit.max' => __('validation.profit.max', ['max' => '999999']),
            'profit.lte' => __('validation.profit.lte'),
            'medicines.required' => __('validation.medicines.required'),
            'medicines.array' => __('validation.medicines.array'),
            'medicines.*.integer' => __('validation.medicines.*.integer'),
            'medicines.*.distinct' => __('validation.medicines.*.distinct'),
            'medicines.*.exists' => __('validation.medicines.*.exists'),
        ];
    }

    public function recalculateTax(): void
    {
        $price = is_numeric($this->price) ? (float) $this->price : 0;
        $profit = is_numeric($this->profit) ? (float) $this->profit : 0;

        if ($price <= 0 || $price === $profit) {
            $this->tax = 0;

            return;
        }

        $this->tax = round($price / 1.18 * 0.18, 2);
    }

    public function sanitized(): array
    {
        $medicines = $this->medicines;
        sort($medicines);

        return [
            'name' => strtoupper(trim($this->name)),
            'description' => $this->description ? strtoupper(trim($this->description)) : null,
            'procedure' => $this->procedure ? strtoupper(trim($this->procedure)) : null,
            'price' => $this->price,
            'tax' => $this->tax,
            'profit' => $this->profit,
            'medicines' => $medicines,
        ];
    }

    protected function rules(): array
    {
        return array_merge($this->stepOneRules(), $this->stepTwoRules());
    }

    protected function stepOneRules(): array
    {
        return [
            'name' => ['required', 'string', 'min:5', 'max:100',
                Rule::unique('treatments', 'name')->ignore($this->treatment?->id)],
            'description' => ['nullable', 'string', 'min:5', 'max:255'],
            'procedure' => ['nullable', 'string', 'min:5', 'max:255'],
            'price' => ['required', 'numeric', 'min:1', 'max:999999'],
            'profit' => ['required', 'numeric', 'min:0', 'max:999999', 'lte:price'],
        ];
    }

    protected function stepTwoRules(): array
    {
        return [
            'medicines' => ['nullable', 'array'],
            'medicines.*' => ['integer', 'distinct', Rule::exists('medicines', 'id')],
        ];
    }
}
