<?php

namespace App\Modules\Users\Auth\Requests\Api;

use App\Modules\BaseApp\Api\Requests\BaseApiParserRequest;

class sendFcmNotificationRequest extends BaseApiParserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.device_token' => 'nullable|string|min:10',
            'attributes.fingerprint' => 'nullable|string|min:5',
        ];
    }
}
