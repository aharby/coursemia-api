<?php

namespace App\Modules\Users\Admin\Requests;

use App\Modules\BaseApp\Requests\BaseAppRequest;
use App\Modules\Users\UserEnums;
use Illuminate\Validation\Rule;
use App\Modules\Users\User;

class UpdateUserRequest extends BaseAppRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'first_name' => 'required|max:190',
            'last_name' => 'required|max:190',
            'email' => [
                'nullable',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('username','=', null)->where('deleted_at', null);
                })->ignore($this->route('id')),
                'email'
            ],
            'mobile' => [
                'nullable',
                'regex:/^(05+[^1-2])+([0-9]){7}+$/',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('username','=', null)->where('deleted_at', null);
                })->ignore($this->route('id')),
            ],
            'password' => 'nullable|min:8|confirmed',
            'is_active' => 'nullable|boolean',
            'hire_date' => 'nullable|date_format:Y-m-d',
            'school_id'=>'nullable|exists:schools,id',
            'country_id'=>'nullable|exists:countries,id'
        ];
         $user = User::find($this->route('id'));
         if ($user->type == UserEnums::SCHOOL_ADMIN) {

            $rules['schools']  = 'required|array|min:1';

        }
        $rules = array_merge($rules, $this->prepareForValidation());
        return $rules;
    }

    /**
     * Edit some user data before validation
     * @return array
     */
    protected function prepareForValidation()
    {
        $additionalRules = [];
        if ($this->type == UserEnums::STUDENT_TYPE) {
            $additionalRules = [
                'school_id'=>'required|exists:schools,id',
                'country_id'=>'required|exists:countries,id',
                'educational_system_id'=>'required|exists:educational_systems,id',
                'grade_class_id'=>'required|exists:grade_classes,id',
                'academical_year_id'=>'required|exists:options,id',
                'birth_date' => 'nullable|date_format:Y-m-d',
            ];
        }
        return $additionalRules;
    }
}
