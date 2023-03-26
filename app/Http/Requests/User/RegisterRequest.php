<?php

namespace App\Http\Requests\User;

use App\Helpers\ResponseFormatter;
use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', new Password],
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    public function failedValidation($validator)
    {
        $errors = $validator->errors()->all();
        throw new \Illuminate\Validation\ValidationException($validator, ResponseFormatter::error($errors, 400));
    }
}
