<?php


namespace App\OurEdu\Assessments\AssessmentManager\Requests;


use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\BaseApp\Api\Requests\BaseApiTokenDataRequest;
use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use Illuminate\Validation\Rule;

class AddAssessmentQuestionRequest extends BaseApiParserRequest
{
    /**
     * @var ParserInterface
     */

    public function rules()
    {
        return [
            'attributes.question_slug'=>[
                'required',
                Rule::in(QuestionTypesEnums::questionTypes())
            ],
            'attributes.category_id' => 'required|integer|exists:assessment_categories,id',
        ];
    }
    public function messages()
    {
        return [
            'attributes.category_id.required' => trans('validation.category_id.required')
        ];
    }
}

