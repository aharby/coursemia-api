<?php

namespace App\Modules\Users\Auth\Requests\Api;


use App\Http\FormRequest;

class ResetUserPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.password' => 'required|confirmed|min:8',
        ];
    }
}
