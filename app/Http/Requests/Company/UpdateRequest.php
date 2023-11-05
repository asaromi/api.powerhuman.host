<?php

namespace App\Http\Requests\Company;

use App\Helpers\ResponseFormatter;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['nullable'],
            'logo' => ['nullable','image','mimes:jpeg,png,jpg,gif,svg','max:2048']
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
