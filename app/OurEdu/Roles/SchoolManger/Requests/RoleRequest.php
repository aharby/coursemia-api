<?php


namespace App\OurEdu\Roles\SchoolManger\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title:*'=>'required',
            'permissions' => 'required',
        ];
    }

}
