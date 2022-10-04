<?php

namespace App\OurEdu\Quizzes\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;

class QuizTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
        'childQuiz',
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
            'classroom_name' => (string) $quiz->classroom->name,
            'quiz_time' => (string) $quiz->quiz_time,
            'show_create_quiz_button' => (bool) (!$quiz->childQuiz and !$quiz->parentQuiz),
            'subject_id' => $quiz->subject_id ,
            'quiz_title' => $quiz->quiz_title,
        ];
        return $transformedData;
    }

    public function includeActions(Quiz $quiz)
    {
        $actions = [];
        if (is_null($quiz->published_at)) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.quizzes.get.quiz.questions', [
                    'quizId' => $quiz->id
                ]),
                'label' => trans('app.quiz questions'),
                'method' => 'GET',
                'key' => APIActionsEnums::EDIT_QUIZ_QUESTIONS
            ];
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.quizzes.get.publish', [
                    'quizId' => $quiz->id
                ]),
                'label' => trans('app.Publish'),
                'method' => 'GET',
                'key' => APIActionsEnums::PUBLISH_QUIZ
            ];
        }
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeChildQuiz(Quiz $quiz) {
        if ($quiz->childQuiz){
            return $this->item($quiz->childQuiz, new QuizTransformer(), ResourceTypesEnums::CHILD_QUIZ);
        }
    }
}
