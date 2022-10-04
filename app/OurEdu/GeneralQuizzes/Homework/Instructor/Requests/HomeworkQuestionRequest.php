<?php


namespace App\OurEdu\GeneralQuizzes\Homework\Instructor\Requests;

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
            'attributes.grade' => 'required|numeric|gt:0',
            'attributes.section_id' => 'required',
            'relationships.general_quiz_questions_data.data.questions.*' => [
                function ($attribute, $question, $fail) {
                    $data = $this->getParser()->deserialize($this->getContent())->getData();
                    $array = explode('.', $attribute);
                    $key = end($array);
                    if ($data->question_slug == QuestionsTypesEnums::DRAG_DROP_TEXT ||
                        $data->question_slug == QuestionsTypesEnums::DRAG_DROP_IMAGE) {
                        return true;
                        /*$dragDropQuestion = $data->general_quiz_questions_data;
                        $this->validateQuestion('question_feedback', $dragDropQuestion, $fail, $key);
                        $this->validateQuestion('description', $dragDropQuestion, $fail, $key);
                        $this->validateQuestionGrade('section_id', $dragDropQuestion, $fail, $key);
                        $this->validateQuestionGrade('grade', $question, $fail, $key);*/
                    } else {
                        $this->validateQuestion('attributes.question', $question, $fail, $key);
                        $this->validateQuestion('attributes.question_feedback', $question, $fail, $key);
                        // $this->validateQuestionGrade('grade', $question, $fail, $key);
                    }
                }
            ],

        ];
    }

    private function validateQuestion($attribute, $question, $fail, $key)
    {
        if (
            !property_exists($question, $attribute)
            || $question->$attribute == '' ||
            ctype_space($question->$attribute)
        ) {
            return $fail(trans('attributes.general_quizzes.required', ['num' => $key + 1, 'field' => trans('general_quizzes.' . $attribute)]));
        }
    }

    public function messages()
    {
        return [
            'attributes.grade.required' => trans('general_quizzes.grade_required'),
            'attributes.section_id.required' => trans('general_quizzes.section_required'),
            'attributes.grade.numeric' => trans('general_quizzes.grade required numeric'),
            'attributes.grade.gt' => trans('general_quizzes.grade more zero'),
        ];
    }

    // private function validateQuestionGrade($attribute, $question, $fail, $key)
    // {
    //     if (!property_exists($question, $attribute) || $question->$attribute == '' || $question->$attribute <= 0) {

    //         return $fail(trans('general_quizzes.required', ['num' => $key + 1, 'field' => $attribute]));
    //     }
    // }
}
