<?php

namespace App\OurEdu\Users\Auth\Requests\api;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class UserActivateOtpRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.otp' => 'required',
        ];
    }
}
