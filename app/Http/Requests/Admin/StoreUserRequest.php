<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-users');
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'  => [
                'required',
                'confirmed',
                Password::min(10)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'role'      => ['required', 'string', 'exists:roles,name'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.min'          => 'Password must be at least 10 characters.',
            'password.mixed_case'   => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers'      => 'Password must contain at least one number.',
            'password.symbols'      => 'Password must contain at least one symbol.',
            'password.uncompromised'=> 'This password has appeared in a data breach. Please choose a different password.',
        ];
    }
}
