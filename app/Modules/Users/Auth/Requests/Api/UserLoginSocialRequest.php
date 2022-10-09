<?php

namespace App\Modules\Users\Auth\Requests\Api;


use Illuminate\Foundation\Http\FormRequest;

class UserLoginSocialRequest extends FormRequest
{
    public function rules()
    {
        return [
            'attributes.token' => 'required',
            'attributes.user_type' => 'nullable|in:student,parent,student_teacher'
        ];
    }
}
