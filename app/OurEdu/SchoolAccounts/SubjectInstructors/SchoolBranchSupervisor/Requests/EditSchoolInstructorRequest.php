<?php
namespace App\OurEdu\SchoolAccounts\SubjectInstructors\SchoolBranchSupervisor\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use Illuminate\Validation\Rule;

class EditSchoolInstructorRequest extends BaseAppRequest
{
    public function rules()
    {

        $rules = [
            'first_name' => 'required|max:190',
            'last_name' => 'required|max:190',
            'email' => [
                'nullable','email',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('deleted_at', null);
                })->ignore($this->route('instructorUserId')),
            ],
            'mobile' => [
                'nullable',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('deleted_at', null);
                })->ignore($this->route('instructorUserId')),
            ],
            'username' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('deleted_at', null);
                })->ignore($this->route('instructorUserId')),
            ],
            'password' => 'nullable|min:8|confirmed',
        ];
        return $rules;
    }
}
