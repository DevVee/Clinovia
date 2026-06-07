<?php

namespace App\Http\Requests\Dispensing;

use Illuminate\Foundation\Http\FormRequest;

class StoreDispensingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-dispensing');
    }

    public function rules(): array
    {
        return [
            'patient_id'      => ['required', 'integer', 'exists:patients,id'],
            'consultation_id' => ['nullable', 'integer', 'exists:consultations,id'],
            'medicine_id'     => ['required', 'integer', 'exists:medicines,id'],
            'quantity'        => ['required', 'integer', 'min:1'],
            'remarks'         => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'patient_id'  => 'patient',
            'medicine_id' => 'medicine',
        ];
    }
}
