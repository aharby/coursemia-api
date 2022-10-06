<?php

namespace App\Modules\Users\Auth\Requests\Api;


use Illuminate\Foundation\Http\FormRequest;

class UserActivateOtpRequest extends FormRequest
{
    public function rules()
    {
        return [
            'attributes.otp' => 'required',
        ];
    }
}
