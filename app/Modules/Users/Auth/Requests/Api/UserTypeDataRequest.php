<?php

namespace App\Modules\Users\Auth\Requests\Api;

use App\Modules\BaseApp\Api\Requests\BaseApiParserRequest;
use App\Modules\Users\UserEnums;
use Illuminate\Validation\Rule;

class UserTypeDataRequest extends BaseApiParserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $default = [
            'attributes.user_type' => [
                Rule::in(UserEnums::getRegistrableUsers())
            ]
        ];
        $parentTypeRules = [
            'attributes.profile_picture' => 'nullable',
            'attributes.country_id' => 'required|integer|exists:countries,id',
        ];
        // student rules
        $studentTypesRules = [
            'attributes.profile_picture' => 'nullable',
            'attributes.country_id' => 'required|integer|exists:countries,id',
            'attributes.educational_system_id' => 'required|integer|exists:educational_systems,id',
            'attributes.class_id' => 'required|integer|exists:grade_classes,id',
            'attributes.school_id' => 'nullable|integer|exists:schools,id',
            'attributes.academical_year_id' => 'required|integer|exists:options,id',
        ];

        $studentTeacherTypesRules = [
            'attributes.profile_picture' => 'nullable',
            'attributes.country_id' => 'required|integer|exists:countries,id',

        ];
        $user_type_validation = $this->json('data')['attributes']['user_type'];

        switch ($user_type_validation) {
            case UserEnums::PARENT_TYPE:
                return array_merge($default, $parentTypeRules);
                break;
            case UserEnums::STUDENT_TYPE:
                return array_merge($default, $studentTypesRules);
                break;
            case UserEnums::STUDENT_TEACHER_TYPE:
                return array_merge($default, $studentTeacherTypesRules);
                break;
            default:
                return $default;
        }
    }
}
