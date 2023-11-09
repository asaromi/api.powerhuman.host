<?php

namespace App\Http\Requests;

use App\Helpers\ResponseFormatter;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     */
    public function failedValidation($validator)
    {
        $errors = $validator->errors()->all();
        throw new \Illuminate\Validation\ValidationException($validator, ResponseFormatter::error($errors, 400));
    }
}
