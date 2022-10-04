<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\Users\UserEnums;
use Illuminate\Validation\Rule;

class UsersRequest extends BaseAppRequest
{
    public function rules()
    {
        $rules =  [
            "first_name" => "required",
            "last_name" => "required",
            "username" => [
                "required",
                Rule::unique("users")->whereNull("deleted_at")
            ],
            "type" => "required"
        ];

        if ($this->filled("email")) {
            $rules["email"] = Rule::unique("users")->whereNull("deleted_at");
        }

        if ($this->filled("type") && $this->get("type") != UserEnums::ASSESSMENT_MANAGER) {
            $rules["branch_id"] = "required";
        }

        if ($this->filled("type") && $this->get("type") == UserEnums::ACADEMIC_COORDINATOR) {
            $rules["role_id"] = "required";
        }

        return $rules;
    }
}
