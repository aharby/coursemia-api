<?php

namespace App\OurEdu\Quizzes\Transformers;

use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Quiz;
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

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(Quiz $quiz)
    {
        $transformedData = [
            'id' => (int) $quiz->id,
            'quiz_type' => (string) $quiz->quiz_type,
            'parent_quiz_id' =>  $quiz->parent_quiz_id,
            'is_published' => (bool) !is_null($quiz->published_at),
            'published_at' => (string) $quiz->published_at,
            'classroom_name' => @(string) $quiz->classroom->name,
            'classroom_id' => (int) $quiz->classroom_id,
            'quiz_time' => (string) $quiz->quiz_time,
        ];

        return $transformedData;
    }

    public function includeActions(Quiz $quiz)
    {
        $actions = [];
        if (is_null($quiz->published_at)) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.quizzes.get.publish', [
                    'quizId' => $quiz->id
                ]),
                'label' => trans('app.Publish'),
                'method' => 'GET',
                'key' => APIActionsEnums::PUBLISH_QUIZ
            ];
        }else{
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.quizzes.get.quiz.students', [
                    'quizId' => $quiz->id
                ]),
                'label' => trans('app.view students results'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_STUDENTS_RESULTS
            ];
        }
         if (count($actions)) {
             return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
         }
    }
}
