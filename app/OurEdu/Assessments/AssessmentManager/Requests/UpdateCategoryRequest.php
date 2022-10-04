<?php

namespace App\OurEdu\Assessments\AssessmentManager\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class UpdateCategoryRequest extends BaseApiParserRequest
{

    public function rules()
    {
        return [
            'attributes.title' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'attributes.title.required' => trans('validation.category_id.required')
        ];
    }
}
