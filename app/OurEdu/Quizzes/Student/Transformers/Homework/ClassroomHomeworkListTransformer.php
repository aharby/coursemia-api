<?php

namespace App\OurEdu\Quizzes\Student\Transformers\Homework;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Quiz;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class ClassroomHomeworkListTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
        'subject'
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
            'classroom_class_session_id' => $homework->classroom_class_session_id,

        ];
        if ($studentQuiz = $this->student->quizzes()->where('quiz_id', $homework->id)->first()) {
            $transformedData['status'] = $studentQuiz->status;
        }else {
            $transformedData['status'] = QuizStatusEnum::NOT_STARTED;
        }

        return $transformedData;
    }

    public function includeActions(Quiz $homework)
    {
        $actions = [];
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.homework.get.view-homework', [
                'homeworkId' => $homework->id,
            ]),
            'label' => trans('app.View'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_HOMEWORK
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
    public function includeSubject(Quiz $quiz)
    {
        if ($quiz->subject) {
            return $this->item($quiz->subject, new QuizSubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }
    }

    private function getQuizStatus($homework)
    {
        // if student started the homework
        if ($studentQuiz = $this->student->quizzes()->where('quiz_id', $homework->id)->first()) {
            // if student != finished the homework => get the first question with its answer
            if ($studentQuiz->status != QuizStatusEnum::FINISHED) {
                return QuizStatusEnum::STARTED;
            }
            return QuizStatusEnum::FINISHED;
        } else {
            return QuizStatusEnum::NOT_STARTED;
        }
    }
}
