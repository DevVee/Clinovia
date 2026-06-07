<?php

namespace App\Http\Requests\Consultation;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-consultations');
    }

    public function rules(): array
    {
        return [
            'patient_id'      => ['required', 'integer', 'exists:patients,id'],
            'appointment_id'  => ['nullable', 'integer', 'exists:appointments,id'],
            'visit_date'      => ['required', 'date'],
            'visit_time'      => ['nullable', 'date_format:H:i'],
            'chief_complaint' => ['required', 'string', 'max:1000'],
            'assessment'      => ['nullable', 'string', 'max:2000'],
            'diagnosis'       => ['nullable', 'string', 'max:500'],
            'treatment'       => ['nullable', 'string', 'max:2000'],
            'notes'           => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'patient_id'      => 'patient',
            'appointment_id'  => 'appointment',
            'visit_date'      => 'visit date',
            'visit_time'      => 'visit time',
            'chief_complaint' => 'chief complaint',
        ];
    }
}
