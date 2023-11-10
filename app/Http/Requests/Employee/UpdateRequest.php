<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
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
            'email' => ['nullable', 'unique:employees'],
            'phone' => ['nullable'],
            'gender' => ['nullable'],
            'age' => ['nullable', 'integer'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'team_id' => ['nullable', 'integer', 'exists:teams,id']
        ];
    }
}
