<?php

namespace App\Modules\Users\Auth\Requests\Api;


use App\Http\FormRequest;

class ConfirmResetCodeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.otp' => 'required|string|exists:users,otp'
        ];
    }

}
