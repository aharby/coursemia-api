<?php

namespace App\OurEdu\Quizzes\Student\Transformers\Homework;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Quiz;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class HomeworkTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
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

    public function transform(Quiz $homework)
    {
        $transformedData = [
            'id' => (int) $homework->id,
            'title' => (string) trans('quiz.homework_title',[
                'subject_name' => $homework->subject->name,
                'creator_name' => $homework->creator->name,
            ]),
            'status' => $this->getQuizStatus($homework),
            'start_at' => (string) $homework->start_at,
            'end_at' => (string) $homework->end_at,
            'classroom_name' =>  (string) $homework->classroom->name,
        ];

        return $transformedData;
    }

    public function includeActions(Quiz $homework)
    {
        $actions = [];

        // if student started the homework
        if ($studentQuiz = $this->student->quizzes()->where('quiz_id', $homework->id)->first()) {
            // if student != finished the homework => get the first question with its answer
            if ($studentQuiz->status != QuizStatusEnum::FINISHED) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.student.homework.get.next-back-questions', [
                        'homeworkId' => $homework->id,
                        'with_answers' => true
                    ]),
                    'label' => trans('app.Continue'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::CONTINUE_HOMEWORK
                ];
            }
        } else {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.homework.get.start-homework', [
                    'homeworkId' => $homework->id,
                ]),
                'label' => trans('app.Start'),
                'method' => 'GET',
                'key' => APIActionsEnums::START_HOMEWORK
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    private function getQuizStatus($homework)
    {
        if ($studentQuiz = $this->student->quizzes()->where('quiz_id', $homework->id)->first()) {
            return $studentQuiz->status;
        } else {
            return QuizStatusEnum::NOT_STARTED;
        }
    }
}
