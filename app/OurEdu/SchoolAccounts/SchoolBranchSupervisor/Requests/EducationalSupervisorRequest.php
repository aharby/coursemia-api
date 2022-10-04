<?php


namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\Users\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EducationalSupervisorRequest extends BaseAppRequest
{
    public function rules()
    {
        $educationalSupervisor = $this->route()->parameter("educational_supervisor");
        $branch_id = Auth::user()->branch_id;

        if ($this->filled("branch_id")) {
            $branch_id = $this->get("branch_id");
        }

        $rules =  [
            "first_name" => "required",
            "last_name" => "required",
            "password" => "nullable|min:8",
            "username" => [
                "required",
                Rule::unique("users", "username")
                    ->whereNot("id", $educationalSupervisor->id)
                    ->whereNull("deleted_at"),

            ]
        ];

        if ($this->filled("email")) {
            $rules["email"] = [
                "email",
                Rule::unique("users", "email")
                    ->whereNot("id", $educationalSupervisor->id)
                    ->whereNull("deleted_at")
                ];
        }

        return $rules;
    }
}
