<?php

namespace App\Http\Requests\Medicine;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-medicines');
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:200'],
            'category_id'         => ['required', 'integer', 'exists:medicine_categories,id'],
            'description'         => ['nullable', 'string', 'max:1000'],
            'quantity'            => ['required', 'integer', 'min:0'],
            'unit'                => ['required', 'string', 'in:tablet,capsule,ml,vial,piece,box,bottle,sachet,other'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'expiration_date'     => ['nullable', 'date'],
            'batch_number'        => ['nullable', 'string', 'max:100'],
            'supplier'            => ['nullable', 'string', 'max:200'],
            'is_active'           => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id'         => 'category',
            'low_stock_threshold' => 'low stock threshold',
            'expiration_date'     => 'expiration date',
            'batch_number'        => 'batch number',
        ];
    }
}
