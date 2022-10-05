<?php

namespace App\Modules\Users\Auth\Requests\Api;

use App\Modules\BaseApp\Api\Requests\BaseApiParserRequest;
use App\Modules\Users\Auth\Enum\DeviceEnum;

class LogoutRequest extends BaseApiParserRequest
{
    protected $deviceTokenRequired = false;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->deviceTokenRequired) {
            return [
                'attributes.fingerprint' => 'required',
            ];
        }

        return [];
    }

    protected function prepareForValidation()
    {
        if ($this->type == DeviceEnum::MOBILE_DEVICE_TYPE) {
            $this->deviceTokenRequired = true;
        }
    }
}
