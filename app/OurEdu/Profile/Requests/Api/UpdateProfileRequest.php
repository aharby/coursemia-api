<?php

namespace App\OurEdu\Profile\Requests\Api;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends BaseApiParserRequest
{
    public function rules()
    {
        $user = User::findOrFail(auth('api')->id());
        $array = [];
        $array['attributes.first_name'] = 'required|max:190';
        $array['attributes.last_name'] = 'required|max:190';
        $array['attributes.language'] = 'required';
        $array['attributes.profile_picture'] = 'nullable|integer|exists:garbage_media,id';
        $array['attributes.mobile'] = [
            'sometimes',
            'regex:/^(05+[^1-2])+([0-9]){7}+$/',
           Rule::unique('users','mobile')->where(
                function ($query) {
                    return $query->where('deleted_at', null);
                }
            )->ignore(auth('api')->id()),
        ];
        if (isset($this->json()->all()['data']['attributes']['email'])) {
            if ($this->json()->all()['data']['attributes']['email'] != $user->email) {
                $array['attributes.email'] = [
                    'sometimes',
                    'email',
                   Rule::unique('users','email')->where(
                        function ($query) {
                            return $query->where('deleted_at', null);
                        }
                    )
                ];
                if(!$user->added_by_social) {
                    $array['attributes.old_password'] = 'required';
                }
            } else {
                $array['attributes.email'] = [
                    'required',
                    'email',
                   Rule::unique('users','email')->where(
                        function ($query) {
                            return $query->where('deleted_at', null);
                        }
                    )->ignore(auth('api')->id())
                ];
            }
        } else {
            $array['attributes.email'] = [
                'sometimes',
                'email',
               Rule::unique('users','email')->where(
                    function ($query) {
                        return $query->where('deleted_at', null);
                    }
                )->ignore(auth('api')->id())
            ];
        }
        // $array['password'] = 'nullable|min:8|confirmed';
        if (!array_key_exists('old_password', $array)) {
            $array['attributes.old_password'] = 'required_with:password';
        }
        if ($user->type == UserEnums::STUDENT_TYPE) {
            $classRoomId = $user->student->classroom_id ?? null;
            if (!$classRoomId) {
                $array['attributes.mobile'] = [
                    'nullable',
                    'regex:/^(05+[^1-2])+([0-9]){7}+$/',
                   Rule::unique('users','mobile')->where(
                        function ($query) {
                            return $query->where('deleted_at', null);
                        }
                    )->ignore(auth('api')->id()),
                ];
                $array['attributes.birth_date'] = 'nullable|date:Y-m-d';
                $array['attributes.educational_system'] = 'required|exists:educational_systems,id';
                $array['attributes.academical_year'] = ['required', Rule::exists('options', 'id')->where(
                    function ($query) {
                        $query->where('type', OptionsTypes::ACADEMIC_YEAR);
                    }
                )];
                $array['attributes.school'] = 'nullable|exists:schools,id';
                $array['attributes.country'] = 'required|exists:countries,id';
                $array['attributes.class'] = 'required|exists:grade_classes,id';
            } else {
                unset($array['attributes.email']);
                unset($array['attributes.mobile']);
            }
        }
        if ($user->type == UserEnums::SCHOOL_INSTRUCTOR) {
            unset($array['attributes.email']);
            unset($array['attributes.mobile']);
        }
        return $array;
    }
}
