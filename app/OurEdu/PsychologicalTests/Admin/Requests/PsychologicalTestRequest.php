<?php

namespace App\OurEdu\PsychologicalTests\Admin\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class PsychologicalTestRequest extends BaseAppRequest
{
    public function rules()
    {
        $rules = [
            'picture'   =>  'required|image',
            'name:en'  =>  'required|string',
            'name:ar'  =>  'required|string',
            'instructions:en'  =>  'required|string',
            'instructions:ar'  =>  'required|string',
            'is_active' =>  'required|boolean'
        ];

        if ($this->route('id')) {
            $rules['picture'] = 'nullable|image';
        }

        return $rules;
    }
}
