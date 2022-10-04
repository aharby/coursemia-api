<?php

namespace App\OurEdu\Users\Auth\Requests\api;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class UserActivateOtp extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.otp' => 'required',
            'attributes.confirm_token' => 'required',
        ];
    }
}

