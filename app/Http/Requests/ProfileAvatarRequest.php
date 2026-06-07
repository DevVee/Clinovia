<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileAvatarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'avatar' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:15360', // 15 MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Please select an image to upload.',
            'avatar.image'    => 'The file must be a valid image.',
            'avatar.mimes'    => 'Allowed formats: JPG, PNG, WebP, GIF.',
            'avatar.max'      => 'Image must be smaller than 15 MB.',
        ];
    }
}
