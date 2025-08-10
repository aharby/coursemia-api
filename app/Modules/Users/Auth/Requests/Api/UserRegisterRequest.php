<?php

namespace App\Modules\Users\Auth\Requests\Api;

use App\Modules\Users\UserEnums;
use App\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $parentTypeRules = [
            'attributes.profile_picture' => 'nullable',
            'attributes.first_name' => 'required',
            'attributes.last_name' => 'required',
            'attributes.email' => [
                'sometimes',
                Rule::unique('users','email')->where(
                    function ($query) {
                        if (\request()->boolean('abilities_user')) {
                            $query->where('username', '=', null);
                        } else {
                            $query->where('username', '!=', null);
                        }
                        return $query->where('deleted_at', null);
                    }
                ),
                'email'
            ],
//            'birth_date' => 'nullable|date_format:Y-m-d',
            'attributes.country_id' => 'required|integer|exists:countries,id',
           'attributes.mobile' => [
                'required',
               'regex:/^(05+[^1-2])+([0-9]){7}+$/',
               Rule::unique('users', 'mobile')->where(function ($query) {
                   if (\request()->boolean('abilities_user')) {
                       $query->where('username', '=', null);
                   } else {
                       $query->where('username', '!=', null);
                   }
                   return $query->where('deleted_at', null);
               }),
           ],
            'attributes.password' => 'required|confirmed|min:8',
            'attributes.password_confirmation' => 'required|min:8'
        ];
        $default = [
            'attributes.user_type' => [
                Rule::in(UserEnums::getRegistrableUsers())
            ]
        ];
        // student rules
        $studentTypesRules = [
            'attributes.profile_picture' => 'nullable',
            'attributes.first_name' => 'required',
            'attributes.last_name' => 'required',
            'attributes.email' => [
                'sometimes',
                Rule::unique('users','email')->where(

                    function ($query) {
                        if (\request()->boolean('abilities_user')) {
                            $query->where('username', '=', null);
                        } else {
                            $query->where('username', '!=', null);
                        }
                        return $query->where('deleted_at', null);
                    }

                ),
                'email'
            ],
//            'birth_date' => 'nullable|date_format:Y-m-d',
            'attributes.country_id' => 'required|integer|exists:countries,id',
           'attributes.mobile' => [
                'required',
                'regex:/^(05+[^1-2])+([0-9]){7}+$/',
               Rule::unique('users', 'mobile')->where(function ($query) {
                   if (\request()->boolean('abilities_user')) {
                       $query->where('username', '=', null);
                   } else {
                       $query->where('username', '!=', null);
                   }
                   return $query->where('deleted_at', null);
               }),
           ],
            'attributes.password' => 'required|confirmed|min:8',
            'attributes.password_confirmation' => 'required|min:8',
            'attributes.educational_system_id' => 'required|integer|exists:educational_systems,id',
            'attributes.class_id' => 'required|integer|exists:grade_classes,id',
            'attributes.school_id' => 'nullable|integer|exists:schools,id',
            'attributes.academical_year_id' => 'required|integer|exists:options,id',
        ];

        // student teacher rules
        $studentTeacherTypesRules = [
            'attributes.profile_picture' => 'nullable',
            'attributes.first_name' => 'required',
            'attributes.last_name' => 'required',
            'attributes.email' => [
                'required',
                Rule::unique('users','email')->where(
                    function ($query) {
                        return $query->where('deleted_at', null);
                    }
                ),
                'email'
            ],
            'attributes.mobile' => 'nullable',
            'attributes.country_id' => 'required|integer|exists:countries,id',
            'attributes.password' => 'required|confirmed|min:8',
            'attributes.password_confirmation' => 'required|min:8',
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
