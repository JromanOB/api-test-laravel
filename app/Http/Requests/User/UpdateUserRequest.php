<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'phonenumber' => ['sometimes', 'string', 'max:20'],
            'fullname' => ['sometimes', 'string', 'max:255'],

            'role_ids' => [
                'sometimes',
                'array',
                'min:1',
            ],

            'role_ids.*' => [
                'integer',
                'distinct',
                'exists:roles,id',
            ],
        ];
    }
}