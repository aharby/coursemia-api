<?php

namespace App\Modules\Users\Auth\Requests\Api;

use App\Modules\BaseApp\Api\Requests\BaseApiParserRequest;

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

