<?php

namespace App\Http\Requests\User;

use App\Helpers\ResponseFormatter;
use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\Rules\Password;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => ['email','required'],
            'password' => ['required', new Password]
        ];
    }

    public function failedValidation($validator)
    {
        $errors = $validator->errors()->all();
        throw new \Illuminate\Validation\ValidationException($validator, ResponseFormatter::error($errors[0], 400));
    }
}
