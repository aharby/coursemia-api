<?php

namespace App\OurEdu\Users\Auth\Requests;
use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\Users\User;
use Illuminate\Validation\Rule;

class ResetPasswordRequest extends BaseAppRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email'
        ];
    }
}
