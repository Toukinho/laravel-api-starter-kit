<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function bodyParameters(): array
    {
        return [
            'email' => ['description' => 'User email.', 'example' => 'user@example.com'],
            'password' => ['description' => 'User password.', 'example' => 'password'],
            'device_name' => ['description' => 'Token label.', 'example' => 'mobile'],
        ];
    }
}
