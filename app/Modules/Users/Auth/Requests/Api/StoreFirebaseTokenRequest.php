<?php

namespace App\Modules\Users\Auth\Requests\Api;


use Illuminate\Foundation\Http\FormRequest;

class StoreFirebaseTokenRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.token' => 'required',
            'attributes.device_token' => 'required',
        ];
    }
}
