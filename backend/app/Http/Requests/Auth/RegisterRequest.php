<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Rules\CaptchaRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'phone_number' => 'required|string|max:20|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'nullable|string|in:user,hr,mentor,admin,super_admin',
        ];

        $isLocalEnvironment = app()->environment(['local', 'testing']);

        if (! $isLocalEnvironment) {
            $rules['captcha'] = ['required', 'string', new CaptchaRule];
        }

        return $rules;
    }
}
