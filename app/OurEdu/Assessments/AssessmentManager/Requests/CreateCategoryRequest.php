<?php

namespace App\OurEdu\Assessments\AssessmentManager\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class CreateCategoryRequest extends BaseApiParserRequest
{

    public function rules()
    {
        return [
            'attributes.title' => 'required|string',
            'attributes.assessment_id' => 'required|integer|exists:assessments,id',
        ];
    }

    public function messages()
    {
        return [
            'attributes.title.required' => trans('validation.category_id.required')
        ];
    }
}
