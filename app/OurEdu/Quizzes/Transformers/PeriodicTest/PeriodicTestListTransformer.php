<?php


namespace App\OurEdu\Quizzes\Transformers\PeriodicTest;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Quiz;
use League\Fractal\TransformerAbstract;

class PeriodicTestListTransformer extends TransformerAbstract
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
            'grade_class_name' => (string) $quiz->gradeClass ?$quiz->gradeClass->title :null,
            'Periodic_Test_Title' => (string) $quiz->quiz_title ,
        ];
        return $transformedData;
    }

    public function includeActions(Quiz $quiz)
    {
        $actions = [];
        if (is_null($quiz->published_at)) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.periodic-test.get.publish', [
                    'periodicTestId' => $quiz->id
                ]),
                'label' => trans('app.Publish'),
                'method' => 'GET',
                'key' => APIActionsEnums::PUBLISH_PERIODIC_TEST
            ];
        }
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

}
