<?php


namespace App\OurEdu\SchoolAdmin\Profile\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class ChangePasswordRequest extends BaseAppRequest
{
    public function rules()
    {
        return [
            'old_password'          => 'required',
            'password'              => 'required|min:8|confirmed',
        ];
    }
}
