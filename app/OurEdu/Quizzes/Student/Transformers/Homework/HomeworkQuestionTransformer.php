<?php

namespace App\OurEdu\Quizzes\Student\Transformers\Homework;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizQuestionsTypesEnum;
use App\OurEdu\Quizzes\Models\QuizQuestion;
use App\OurEdu\Quizzes\Models\QuizQuestionAnswer;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class HomeworkQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];
    protected array $availableIncludes = [
    ];

    protected $params;
    protected $student;

    public function __construct($params = [])
    {
        $this->params = $params;
        $this->student = Auth::guard('api')->user()->student;
    }

    public function transform(QuizQuestion $quizQuestion)
    {
        $transformedData = [
            'id' => (int) $quizQuestion->id,
            'quiz_id' => (string) $quizQuestion->quiz_id,
            'time_to_solve' => (string) $quizQuestion->time_to_solve,
            'question_type' => (string) $quizQuestion->question_type,
            'question_text' => (string) $quizQuestion->question_text,
            'question_grade' => (integer) $quizQuestion->question_grade,
            'direction'=>$quizQuestion->quiz->subject->direction,
        ];
        $answers = QuizQuestionAnswer::query()
            ->where('question_id', $quizQuestion->id)
            ->where('student_id', $this->student->id)
            ->where('quiz_id',$quizQuestion->quiz_id)
            ->get();
        $selectedOptions = $answers->isNotEmpty()?array_unique($answers->pluck('option_id')->toArray()):[];
        $options = [];
        foreach ($quizQuestion->options as $option) {
            $optionData =
                [
                    'id' => $option->id,
                    'option' => $option->option,
                    'isSelected'=>in_array($option->id,$selectedOptions)?true:false
                ];
            $options[] = $optionData;
        }
        $transformedData['options'] = $options;

        if (isset($this->params['with_answers']) && $this->params['with_answers']) {
            if ($answers->isNotEmpty()){
                $answersArray = [];
                foreach ($answers as $answer) {
                    $answerArray = [
                        'option_id' => $answer->option->id,
                        'option' => $answer->option->option,
                        'is_correct_answer' => $answer->is_correct_answer,
                    ];
                    if ($quizQuestion->question_type == QuizQuestionsTypesEnum::MULTIPLE_CHOICE) {
                        $answerArray['is_correct_option'] = $answer->is_correct_option;
                    }
                    $answersArray[] = $answerArray;
                }
                $transformedData['answers'] = $answersArray;
            }
        }
        return $transformedData;
    }

    public function includeActions(QuizQuestion $quizQuestion)
    {
        $actions = [];

        if (isset($this->params['next'])) {
            $actions[] = [
                'endpoint_url' => $this->params['next'],
                'label' => trans('exam.Next Question'),
                'method' => 'GET',
                'key' => APIActionsEnums::NEXT_QUESTION
            ];
        }

        if (isset($this->params['previous'])) {
            $actions[] = [
                'endpoint_url' => $this->params['previous'],
                'label' => trans('exam.Previous Question'),
                'method' => 'GET',
                'key' => APIActionsEnums::PREVIOUS_QUESTION
            ];
        }


        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.homework.get.finish-homework',
                ['homeworkId' => $quizQuestion->quiz_id]),
            'label' => trans('exam.Finish Homework'),
            'method' => 'POST',
            'key' => APIActionsEnums::FINISH_HOMEWORK
        ];
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.homework.post.answer',
                ['homeworkId' => $quizQuestion->quiz_id]),
            'label' => trans('exam.Post answer'),
            'method' => 'POST',
            'key' => APIActionsEnums::POST_ANSWER
        ];
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
