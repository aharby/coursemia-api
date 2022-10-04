<?php

namespace App\OurEdu\GradeClasses\Admin\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class GradeClassRequest extends BaseAppRequest
{
    public function rules()
    {
        return [
            'title:ar' => 'required|max:250',
            'title:en' => 'required|max:250',
            'country_id' => 'required',
            'educational_system_id' => 'required',
            'is_active' => 'required|boolean'
        ];
    }
}
