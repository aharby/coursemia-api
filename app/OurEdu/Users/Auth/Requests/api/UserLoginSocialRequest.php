<?php

namespace App\OurEdu\Users\Auth\Requests\api;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class UserLoginSocialRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.token' => 'required',
            'attributes.user_type' => 'nullable|in:student,parent,student_teacher'
        ];
    }
}
