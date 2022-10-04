<?php


namespace App\OurEdu\Assessments\AssessmentManager\Requests;


use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use Illuminate\Validation\Rule;
use App\OurEdu\Users\UserEnums;

class CreateAssessmentRequest extends BaseApiParserRequest
{
    public function rules()
    {
        $data = request()->get('data')['attributes'];
        return [
            'attributes.start_at' => 'required|date|before:attributes.end_at|after:' . now(),
            'attributes.end_at' => 'required|date|after:attributes.start_date',
            'attributes.title' => 'required|string',
            'attributes.introduction' => 'nullable|string',
            'attributes.assessor_type' => ['required', Rule::in(UserEnums::assessmentUsers())],
            'attributes.assessee_type' => ['required', Rule::in(UserEnums::assessmentUsers())],
            'attributes.assessment_viewer_type' => ['nullable', Rule::in(UserEnums::assessmentUsers())]
        ];
    }

    public function messages()
    {
        return [
            'attributes.assessor_type.required'=>trans('assessment.field_required',[
                'field'=>trans('assessment.assessor_type')
            ]),
            'attributes.assessee_type.required'=>trans('assessment.field_required',[
                'field'=>trans('assessment.assessee_type')
            ]),
            'attributes.assessment_viewer_type.required'=>trans('assessment.field_required',[
                'field'=>trans('assessment.assessment_viewer_type')
            ]),
        ];
    }
}
