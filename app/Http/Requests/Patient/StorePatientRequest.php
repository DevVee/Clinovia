<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-patients');
    }

    public function rules(): array
    {
        return [
            // Personal
            'category'          => ['required', 'in:college,senior_high,junior_high,elementary,kinder,daycare,teacher,employee,visitor,other'],
            'first_name'        => ['required', 'string', 'max:100'],
            'middle_name'       => ['nullable', 'string', 'max:100'],
            'last_name'         => ['required', 'string', 'max:100'],
            'suffix'            => ['nullable', 'string', 'max:20'],
            'sex'               => ['required', 'in:male,female'],
            'birthdate'         => ['required', 'date', 'before:today'],
            'contact_number'    => ['nullable', 'string', 'max:20'],
            'email'             => ['nullable', 'email', 'max:150'],
            'address'           => ['nullable', 'string', 'max:500'],
            'emergency_contact_name'   => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:20'],

            // Academic
            'year_level'        => ['nullable', 'string', 'max:50'],
            'program_strand'    => ['nullable', 'string', 'max:100'],
            'section'           => ['nullable', 'string', 'max:50'],

            // Guardian
            'guardian_name'         => ['nullable', 'string', 'max:150'],
            'guardian_relationship' => ['nullable', 'string', 'max:50'],
            'guardian_contact'      => ['nullable', 'string', 'max:20'],
            'guardian_address'      => ['nullable', 'string', 'max:500'],

            // Medical
            'blood_type'          => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-,Unknown'],
            'allergies'           => ['nullable', 'string'],
            'medical_conditions'  => ['nullable', 'string'],
            'notes'               => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'category.required'   => 'Please select a patient category.',
            'first_name.required' => 'First name is required.',
            'last_name.required'  => 'Last name is required.',
            'sex.required'        => 'Sex is required.',
            'birthdate.required'  => 'Birthdate is required.',
            'birthdate.before'    => 'Birthdate must be in the past.',
        ];
    }
}
