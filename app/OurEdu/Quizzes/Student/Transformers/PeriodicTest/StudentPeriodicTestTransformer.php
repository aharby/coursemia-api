<?php


namespace App\OurEdu\Quizzes\Student\Transformers\PeriodicTest;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Quiz;
use League\Fractal\TransformerAbstract;

class StudentPeriodicTestTransformer extends TransformerAbstract
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

    public function transform(Quiz $periodicTest)
    {
        $transformedData = [
            'id' => (int) $periodicTest->id,
            'quiz_type' => (string) $periodicTest->quiz_type,
            'quiz_title'=>(string) $periodicTest->quiz_title,
            'start_at' => (string) $periodicTest->start_at,
            'end_at' => (string) $periodicTest->end_at,
            'subject_name'=> (string) $periodicTest->subject->name,
            'grade_name' => (string) ($periodicTest->gradeClass->title) ?? "",
            'instructor_name' => $periodicTest->creator && $periodicTest->creator->type == 'school_instructor'?$periodicTest->creator->first_name.' '.$periodicTest->creator->last_name:'-',
        ];

        return $transformedData;
    }

    public function includeActions(Quiz $quiz)
    {
        $actions = [];
        // if (isset($this->params['start_quiz'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.periodic-test.get.start-periodic-test', [
                    'periodicTestId' => $quiz->id
                ]),
                'label' => trans('app.Start'),
                'method' => 'GET',
                'key' => APIActionsEnums::START_PERIODIC_TEST
            ];
        // }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
