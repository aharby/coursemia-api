<?php


namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StudentParentRequest extends BaseAppRequest
{
    public function rules()
    {
        $student = $this->route()->parameter("student");
        $parent = $this->route()->parameter("parent");

        $rules =  [
            "first_name" => "required",
            "last_name" => "required",
            "username" => [
                "required",
                function ($attribute, $value, $fail) use ($parent){
                    $parentFromUsername = User::query()
                        ->where("username", "=", $value)
                        ->when(isset($parent), function (Builder $builder) use ($parent) {
                            $builder->where("id", "!=", $parent->id);
                        })
                        ->first();

                    if (isset($parentFromUsername) and $parentFromUsername->type != UserEnums::PARENT_TYPE) {
                        $fail(trans("parents.the username exists and this user, not one of parents"));
                    }

                    if (isset($parent) and isset($parentFromUsername)) {
                        $fail(trans("parents.the username exists for another parent"));
                    }
                },
            ]
        ];

        if ($this->filled("email")) {
            $rules["email"] = [
                "email",
                Rule::unique("users", "email")
                    ->whereNot("id", $student->user_id)
                    ->whereNull("deleted_at")
                    ->where('username', '!=', null)
                    ->ignore($parent)
            ];
        }

        return $rules;
    }
}
