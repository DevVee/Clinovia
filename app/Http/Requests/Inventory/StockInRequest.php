<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StockInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-inventory');
    }

    public function rules(): array
    {
        return [
            'medicine_id'     => ['required', 'integer', 'exists:medicines,id'],
            'quantity'        => ['required', 'integer', 'min:1'],
            'batch_number'    => ['nullable', 'string', 'max:100'],
            'expiration_date' => ['nullable', 'date'],
            'supplier'        => ['nullable', 'string', 'max:200'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'medicine_id'     => 'medicine',
            'expiration_date' => 'expiration date',
            'batch_number'    => 'batch / lot number',
        ];
    }
}
