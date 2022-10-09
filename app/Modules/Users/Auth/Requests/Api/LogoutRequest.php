<?php

namespace App\Modules\Users\Auth\Requests\Api;

use App\Modules\Users\Auth\Enum\DeviceEnum;
use Illuminate\Foundation\Http\FormRequest;

class LogoutRequest extends FormRequest
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
