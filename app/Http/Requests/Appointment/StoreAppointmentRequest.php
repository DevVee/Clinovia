<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-appointments');
    }

    public function rules(): array
    {
        return [
            'patient_id'       => ['required', 'exists:patients,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'string'],
            'purpose'          => ['required', 'string', 'max:500'],
            'notes'            => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required'       => 'Please select a patient.',
            'patient_id.exists'         => 'Selected patient not found.',
            'appointment_date.required' => 'Appointment date is required.',
            'appointment_date.after_or_equal' => 'Appointment date must be today or in the future.',
            'appointment_time.required' => 'Please select a time slot.',
            'purpose.required'          => 'Please describe the purpose of the visit.',
        ];
    }
}
