<?php


namespace App\OurEdu\Assessments\Assessor\Requests;


use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use Illuminate\Validation\Rule;

class PostAnswerRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.question_type'=>[
                'required',
                Rule::in(QuestionTypesEnums::questionTypes())
            ],
            'attributes.assessee_id'=>[
                'required',
                function ($attribute, $value, $fail) {
                    $attributes = $this->request->get('data')['attributes'];

                    if (!in_array($attributes['assessee_id'],$this->assesseesIds)) {
                        $fail($attribute .' '. trans('assessment.invalid assessee'));
                    }
                },
            ]

        ];
    }



    protected function prepareForValidation()
    {
        if ($assessment = $this->route('assessment')) {

            $this->assesseesIds = $assessment->assessees->pluck('id')->toArray();
        }
    }

}
