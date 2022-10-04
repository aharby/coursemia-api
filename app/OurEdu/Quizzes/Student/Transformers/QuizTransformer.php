<?php

namespace App\OurEdu\Quizzes\Student\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Quiz;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class QuizTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];
    protected array $availableIncludes = [
    ];

    protected $params;
    protected $isFailed;
    protected $student;


    public function __construct($params = [])
    {
        $this->params = $params;
        if (isset($this->params['result'])) {
            $this->isFailed = $this->params['result'] < 50;
        }
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
        ];

        if (isset($this->params['result'])) {
            $transformedData['result'] = $this->params['result'];
            $transformedData['status'] = $this->isFailed ? trans('quiz.Failed') : trans('quiz.Success');
        }

        return $transformedData;
    }

    public function includeActions(Quiz $quiz)
    {
        $actions = [];
        // if student started the homework
        if ($studentQuiz = $this->student->quizzes()->where('quiz_id', $quiz->id)->first()) {
            // if student != finished the quiz => get the first question
            if ($studentQuiz->status != QuizStatusEnum::FINISHED) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.student.quizzes.get.next-back-questions', [
                        'quizId' => $quiz->id,
                    ]),
                    'label' => trans('app.Continue'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::CONTINUE_QUIZ
                ];
            }
        } else {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.quizzes.get.start-quiz', [
                    'quizId' => $quiz->id
                ]),
                'label' => trans('app.Start'),
                'method' => 'GET',
                'key' => APIActionsEnums::START_QUIZ
            ];
        }
        if (isset($this->isFailed) && $this->isFailed) {
            if ($quiz->childQuiz) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.student.quizzes.get.quiz', [
                        'quizId' => $quiz->childQuiz->id
                    ]),
                    'label' => trans('quiz.Retake quiz'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::RETAKE_QUIZ
                ];
            }
        }
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
