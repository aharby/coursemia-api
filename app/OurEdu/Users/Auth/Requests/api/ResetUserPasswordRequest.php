<?php

namespace App\OurEdu\Users\Auth\Requests\api;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class ResetUserPasswordRequest extends BaseApiParserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.password' => 'required|confirmed|min:8',
        ];
    }
}
