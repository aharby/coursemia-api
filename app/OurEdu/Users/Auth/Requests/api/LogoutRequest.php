<?php

namespace App\OurEdu\Users\Auth\Requests\api;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Users\Auth\Enum\DeviceEnum;

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
