<?php

namespace App\Modules\Users\Auth\Requests\Api;


use Illuminate\Foundation\Http\FormRequest;

class UserActivateOtp extends FormRequest
{
    public function rules()
    {
        return [
            'attributes.otp' => 'required',
            'attributes.confirm_token' => 'required',
        ];
    }
}

