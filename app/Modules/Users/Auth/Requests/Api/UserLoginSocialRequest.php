<?php

namespace App\Modules\Users\Auth\Requests\Api;

use App\Modules\BaseApp\Api\Requests\BaseApiParserRequest;

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
