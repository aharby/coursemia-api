<?php

namespace App\OurEdu\Users\Auth\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class UserLoginRequest extends BaseAppRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required',
            'password' => ''
        ];
    }
}
