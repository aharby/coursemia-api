<?php

namespace App\Modules\Users\Auth\Requests;
use App\Modules\BaseApp\Requests\BaseAppRequest;
use App\Modules\Users\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

class UpdatePasswordRequest extends BaseAppRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|max:190',
            'last_name' => 'required|max:190',
            'email' => ['required',
                'email',
                Rule::unique("users")->where(function (Builder $query) {
                    return $query->whereNull("deleted_at")
                        ->where("confirm_token", "!=", $this->route()->parameter("confirmToken"));
                })
                ],
            'password' => 'required|min:8|confirmed'
        ];
    }
}
