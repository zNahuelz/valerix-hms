<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class SupplierIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'trashed' => ['sometimes', 'boolean'],
            'id' => ['sometimes', 'integer'],
            'name' => ['sometimes', 'string', 'max:150'],
            'ruc' => ['sometimes', 'string', 'max:15'],
            'email' => ['sometimes', 'string', 'max:50'],
            'sort_by' => ['sometimes', 'in:id,name,ruc,email,created_at,updated_at'],
            'sort_dir' => ['sometimes', 'in:asc,desc'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
