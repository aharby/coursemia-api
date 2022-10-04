<?php

namespace App\OurEdu\Invitations\Student\Requests\Api;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InvitationRequest extends BaseApiParserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
            $rules = [];

            if ($this->user->type == UserEnums::STUDENT_TYPE && $this->type == UserEnums::STUDENT_TEACHER_TYPE) {
                $rules['attributes.subjects'] = ['required', 'array', Rule::in($this->availableSubjects)];
            }


        return $rules;
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

            $this->availableSubjects = $this->user->student->subjects->pluck('id')->toArray() ?? [-234];
        }
    }

}
