<?php

namespace App\Http\Requests\Team;

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
            'company_id' => ['nullable','integer','exists:companies,id'],
            'icon' => ['nullable','image','mimes:jpeg,png,jpg,gif,svg','max:2048']
        ];
    }
}
