<?php

namespace App\Modules\Users\Auth\Requests;

use App\Modules\BaseApp\Requests\BaseAppRequest;

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
