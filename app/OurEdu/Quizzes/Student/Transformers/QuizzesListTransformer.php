<?php

namespace App\OurEdu\Quizzes\Student\Transformers;

use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Quiz;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;

class QuizzesListTransformer extends TransformerAbstract
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

    public function transform(Quiz $quiz)
    {
        $transformedData = [
            'id' => (int) $quiz->id,
            'quiz_type' => (string) $quiz->quiz_type,
            'start_at' => (string) $quiz->start_at,
            'end_at' => (string) $quiz->end_at,
            'classroom_name' =>  (string) $quiz->classroom->name,
            'quiz_time' =>  (string) $quiz->quiz_time,
            'is_taken_and_finished' =>  (bool) $this->isQuizTaken($quiz, $this->student)
        ];

        return $transformedData;
    }

    public function includeActions(Quiz $quiz)
    {
        $actions = [];
        if ($quiz->published_at) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.quizzes.get.quiz', [
                    'quizId' => $quiz->id
                ]),
                'label' => trans('app.View'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_QUIZ
            ];
        }
         if (count($actions)) {
             return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
         }
    }

    private function isQuizTaken($quiz, $student)
    {
        if ($takenQuiz = $student->quizzes()->where('quiz_id', $quiz->id)->first()) {
            return $takenQuiz->status == QuizStatusEnum::FINISHED;
        }
        return false;
    }
}
