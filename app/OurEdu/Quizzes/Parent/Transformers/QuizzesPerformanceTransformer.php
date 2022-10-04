<?php

namespace App\OurEdu\Quizzes\Parent\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Models\AllQuizStudent;
use App\OurEdu\Quizzes\Parent\QuizzesPerformance;
use App\OurEdu\Quizzes\Quiz;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class QuizzesPerformanceTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
    ];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(QuizzesPerformance $quizzesPerformance)
    {
        $transformedData = [
            'id' =>  Str::uuid(),
            'completed_homework_percentage' => $quizzesPerformance->completed_homework_percentage,
        ];
        return $transformedData;
    }

    public function includeActions(QuizzesPerformance $quizzesPerformance)
    {
        $actions = [];
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.parent.quizzes.listStudentQuizzes', [
                'studentId' => $quizzesPerformance->student_id
            ]),
            'label' => trans('quiz.list quizzes'),
            'method' => 'GET',
            'key' => APIActionsEnums::LIST_QUIZZES
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
