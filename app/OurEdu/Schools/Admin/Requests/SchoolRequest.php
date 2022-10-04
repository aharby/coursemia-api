<?php


namespace App\OurEdu\Schools\Admin\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class SchoolRequest extends BaseAppRequest
{
    public function rules()
    {
        return [
            'name:*' => 'required|max:190',
            'country_id' => 'required|exists:countries,id',
            'educational_system_id' => 'required|exists:educational_systems,id',
            'address' => 'required|max:255',
            'email' => 'nullable|email',
            'mobile' => 'nullable|required',
            'is_active' => 'required|boolean'
        ];
    }
}