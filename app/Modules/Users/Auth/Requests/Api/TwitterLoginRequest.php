<?php

namespace App\Modules\Users\Auth\Requests\Api;

use App\Modules\BaseApp\Api\Requests\BaseApiParserRequest;

class TwitterLoginRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
//            'access_token' =>  'required',
//            'access_token_secret' =>  'required',
            'attributes.user_type' => 'required|in:student,parent,student_teacher'
        ];
    }
}
