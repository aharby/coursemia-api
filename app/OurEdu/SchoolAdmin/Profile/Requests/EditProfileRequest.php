<?php


namespace App\OurEdu\SchoolAdmin\Profile\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EditProfileRequest extends BaseAppRequest
{
    public function rules()
    {
        $rules =  [
            "first_name" => "required",
            "last_name" => "required",
            "mobile" => "nullable",
            "username" => [
                "required",
                Rule::unique("users", "username")
                    ->whereNot("id", Auth::id())
                    ->whereNull("deleted_at"),
                ]
        ];

        if ($this->filled("email")) {
            $rules["email"] = [
                "email",
                Rule::unique("users", "email")
                    ->whereNot("id", Auth::id())
                    ->whereNull("deleted_at"),
                ];
        }

        return $rules;
    }
}
