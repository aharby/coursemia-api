<?php


namespace App\Modules\Users\Auth\Requests\Api;

use App\Modules\BaseApp\Api\Requests\BaseApiParserRequest;

class TwitterStatelessCallbackRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.oauth_token' => 'required',
            'attributes.user' => 'required',
            'attributes.oauth_verifier' => 'required',
            'attributes.user_type' => 'nullable|in:student,parent,student_teacher'
        ];
    }
}
