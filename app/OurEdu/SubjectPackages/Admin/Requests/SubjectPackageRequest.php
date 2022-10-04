<?php

namespace App\OurEdu\SubjectPackages\Admin\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class SubjectPackageRequest extends BaseAppRequest
{
    public function rules()
    {
        $rules = [
            'name' => 'required|max:250',
            'description' => 'required',
            'price' => 'numeric|min:1|required',
            'country_id' => 'required|integer|exists:countries,id',
            'educational_system_id' => 'required|integer|exists:educational_systems,id',
            'grade_class_id' => 'required|integer|exists:grade_classes,id',
            'academical_years_id' => 'required|integer|exists:options,id',
            'is_active' => 'required|boolean',
        ];

        if ($this->route('id')) {
            $rules["picture"] = ['image'];
        } else {
            $rules["picture"] = ['required', 'image'];
        }

        return $rules;
    }
}
