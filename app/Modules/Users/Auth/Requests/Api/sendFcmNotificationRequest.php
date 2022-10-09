<?php

namespace App\Modules\Users\Auth\Requests\Api;


use Illuminate\Foundation\Http\FormRequest;

class sendFcmNotificationRequest extends FormRequest
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
