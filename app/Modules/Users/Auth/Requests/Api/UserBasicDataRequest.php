<?php

namespace App\Modules\Users\Auth\Requests\Api;

use App\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserBasicDataRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.profile_picture' => 'nullable',
            'attributes.first_name' => 'required',
            'attributes.last_name' => 'required',
            'attributes.email' => [
                'required',
                Rule::unique('users')->where(
                    function ($query) {
                        return $query->where('deleted_at', null);
                    }
                ),
                'email'
            ],
            'attributes.password' => 'required|confirmed|min:8',
            'attributes.password_confirmation' => 'required|min:8'
        ];
    }
}
