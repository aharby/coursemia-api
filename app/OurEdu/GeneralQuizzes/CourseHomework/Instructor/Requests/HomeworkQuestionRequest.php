<?php


namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class HomeworkQuestionRequest extends BaseApiParserRequest
{
    /**
     * @var ParserInterface
     */

    public function rules()
    {
        return [
            'attributes' => 'required',
            'attributes.grade' => 'required|integer|min:0|not_in:0',
            'relationships.general_quiz_questions_data.data.questions.*' => [
                function ($attribute, $question, $fail) {
                    $data = $this->getParser()->deserialize($this->getContent())->getData();
                    $array = explode('.', $attribute);
                    $key = end($array);
                    if ($data->question_slug == QuestionsTypesEnums::DRAG_DROP_TEXT ||
                        $data->question_slug == QuestionsTypesEnums::DRAG_DROP_IMAGE) {
                        return true;
                    } else {
                        $this->validateQuestion('attributes.question', $question, $fail, $key);
                        $this->validateQuestion('attributes.question_feedback', $question, $fail, $key);
                    }
                }
            ],

        ];
    }

    public function messages()
    {
        return [
            'attributes.grade.required' => trans('general_quizzes.grade_required'),
            'attributes.grade.numeric' => trans('general_quizzes.grade required numeric'),
            'attributes.grade.not_in' => trans('general_quizzes.grade more zero'),

        ];
    }
}
