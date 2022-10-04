<?php

namespace App\OurEdu\Invitations\Requests;

use App\OurEdu\Users\UserEnums;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class SearchForInvitationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'q' => 'required|email',
            'type'  =>  [
                'required',
                Rule::in($this->supportedTypes)
            ]
        ];
    }

    protected function prepareForValidation()
    {
        $this->user = Auth::guard('api')->user();

        if ($this->user->type == UserEnums::STUDENT_TEACHER_TYPE) {
            $this->supportedTypes = [UserEnums::STUDENT_TYPE];
        }

        if ($this->user->type == UserEnums::PARENT_TYPE) {
            $this->supportedTypes = [UserEnums::STUDENT_TYPE];
        }

        if ($this->user->type == UserEnums::STUDENT_TYPE) {
            $this->supportedTypes = [UserEnums::STUDENT_TEACHER_TYPE, UserEnums::PARENT_TYPE];
        }
    }
}
