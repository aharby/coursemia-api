<?php

namespace App\Modules\Users\Auth\Requests\Api;

use App\Modules\BaseApp\Api\Requests\BaseApiParserRequest;

class UserActivateOtpRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.otp' => 'required',
        ];
    }
}
