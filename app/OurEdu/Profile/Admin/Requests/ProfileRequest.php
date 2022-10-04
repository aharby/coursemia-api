<?php

namespace App\OurEdu\Profile\Admin\Requests;

use App\OurEdu\Users\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = User::findOrFail(auth()->id());

        $rules = [];
        $rules['language'] = 'required';



        $rules['first_name'] = 'required|regex:/^[\pL\s\d]+$/u|max:191|min:3';
        $rules['last_name'] = 'required|regex:/^[\pL\s\d]+$/u|max:191|min:3';


        $rules['email']=  [
            'required', 'email',Rule::unique('users')->where(function ($query) {
                return $query->where('deleted_at', null);
            })->ignore(auth()->id()),
        ];
        $rules['password'] = 'nullable|min:8|confirmed';
        $rules['old_password'] = 'required_with:password';


        if($user->email != $this->email)
        {
            $rules['old_password'] = 'required|min:8';
        }

        return $rules;

    }
}
