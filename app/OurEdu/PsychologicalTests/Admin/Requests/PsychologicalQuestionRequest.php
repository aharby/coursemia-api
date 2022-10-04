<?php

namespace App\OurEdu\PsychologicalTests\Admin\Requests;

use App\OurEdu\BaseApp\Requests\BaseAppRequest;

class PsychologicalQuestionRequest extends BaseAppRequest
{
    public function rules()
    {
        return [
            'name:en'  =>  'required|string',
            'name:ar'  =>  'required|string',
            'is_active'	=>	'required|boolean'
        ];
    }
}
