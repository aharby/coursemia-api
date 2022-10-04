<?php

namespace App\OurEdu\Quizzes\Student\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Models\QuizQuestion;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Quizzes\Models\QuizQuestionAnswer;

class QuizQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];
    protected array $availableIncludes = [
    ];

    protected $params;

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
            'question_type' => (string) $quizQuestion->question_type,
            'question_text' => (string) $quizQuestion->question_text,
            'question_grade' => (integer) $quizQuestion->question_grade,
            'time_to_solve' => (integer) $quizQuestion->time_to_solve,
            'direction'=>$quizQuestion->quiz->subject->direction,
        ];

        $selectedOptions = array_unique(QuizQuestionAnswer::query()
            ->where('student_id',$this->student->id)
            ->where('question_id',$quizQuestion->id)
            ->where('quiz_id',$quizQuestion->quiz_id)
            ->pluck('option_id')->toArray());
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

        if (isset($this->params['last_question'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.quizzes.get.finish-quiz',
                    ['quizId' => $quizQuestion->quiz_id]),
                'label' => trans('exam.Finish Quiz'),
                'method' => 'POST',
                'key' => APIActionsEnums::FINISH_QUIZ
            ];
        }
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.quizzes.post.answer',
                ['quizId' => $quizQuestion->quiz_id]),
            'label' => trans('exam.Post answer'),
            'method' => 'POST',
            'key' => APIActionsEnums::POST_ANSWER
        ];
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
