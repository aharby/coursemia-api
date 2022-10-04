<?php


namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\Users\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StudentRequest extends BaseAppRequest
{
    public function rules()
    {
        $student = $this->route()->parameter("student");
        $branch_id = Auth::user()->branch_id;

        if ($this->filled("branch_id")) {
            $branch_id = $this->get("branch_id");
        }

        $rules =  [
            "first_name" => "required",
            "last_name" => "required",
            "password" => "nullable|min:8",
            "classroom_id" => [
                "required",
                Rule::exists("classrooms","id")
                    ->where("branch_id", $branch_id)
                ],
            "username" => [
                "required",
                Rule::unique("users", "username")
                    ->whereNot("id", $student->user_id)
                    ->whereNull("deleted_at"),

            ]
        ];

        if ($this->filled("email")) {
            $rules["email"] = [
                "email",
                Rule::unique("users", "email")
                    ->whereNot("id", $student->user_id)
                    ->whereNull("deleted_at")
                    ->where('username', '!=', null)
                ];
        }

        return $rules;
    }
}
