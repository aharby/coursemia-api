<?php


namespace App\OurEdu\Quizzes\Transformers\HomeWork;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Quiz;
use League\Fractal\TransformerAbstract;

class HomeWorkTransformer extends TransformerAbstract
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
            'start_at' => (string) $quiz->start_at,
            'end_at' => (string) $quiz->end_at,
            'is_published' => (bool) !is_null($quiz->published_at),
            'published_at' => (string) $quiz->published_at,
            'classroom_name' => (string) $quiz->classroom->name,
            'Home_work_title' => $quiz->quiz_title,
            'classroom_class_session_id' => $quiz->classroom_class_session_id,
        ];
        return $transformedData;
    }

    public function includeActions(Quiz $quiz)
    {
        $actions = [];
        if (is_null($quiz->published_at)) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.homework.get.homework.questions', [
                    'homeworkId' => $quiz->id
                ]),
                'label' => trans('app.Edit HomeWork questions'),
                'method' => 'GET',
                'key' => APIActionsEnums::EDIT_HOMEWORK_QUESTIONS
            ];
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.homework.get.publish', [
                    'homeworkId' => $quiz->id
                ]),
                'label' => trans('app.Publish'),
                'method' => 'GET',
                'key' => APIActionsEnums::PUBLISH_HOMEWORK
            ];
        }
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

}
