<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->user?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId)
            ],
            'password' => [
                $this->isMethod('POST') ? 'required' : 'sometimes',
                'string',
                'min:8',
                'confirmed'
            ],
            'role' => [
                'required',
                Rule::in([
                    User::ROLE_STUDENT,
                    User::ROLE_INSTRUCTOR,
                    User::ROLE_SUPER_ADMIN,
                    User::ROLE_ACADEMY_ADMIN,
                    User::ROLE_MENTOR,
                    User::ROLE_CONTENT_EDITOR,
                    User::ROLE_AFFILIATE_AMBASSADOR,
                    
                ])
            ],
            'date_of_birth' => ['nullable', 'date', 'before:-13 years'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_city' => ['nullable', 'string', 'max:255'],
            'address_state' => ['nullable', 'string', 'max:255'],
            'address_country' => ['nullable', 'string', 'max:255'],
            'address_postal_code' => ['nullable', 'string', 'max:20'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'education_level' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'social_links.*' => ['nullable', 'url']
        ];
    }

    public function messages()
    {
        return [
            'date_of_birth.before' => 'You must be at least 13 years old to register.',
            'social_links.*.url' => 'The social media link must be a valid URL.'
        ];
    }
}