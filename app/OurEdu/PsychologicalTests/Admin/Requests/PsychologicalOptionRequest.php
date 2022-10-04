<?php

namespace App\OurEdu\PsychologicalTests\Admin\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class PsychologicalOptionRequest extends BaseAppRequest
{
    public function rules()
    {
        return [
            'name:en'  =>  'required|string',
            'name:ar'  =>  'required|string',
            'points'  =>  'required|numeric|min:0',
            'is_active'	=>	'required|boolean'
        ];
    }
}
