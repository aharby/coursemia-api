<?php

namespace App\Modules\Users\Auth\Requests\Api;


use App\Http\FormRequest;

class UserActivateOtpRequest extends FormRequest
{
    public function rules()
    {
        return [
            'attributes.otp' => 'required',
        ];
    }
}
