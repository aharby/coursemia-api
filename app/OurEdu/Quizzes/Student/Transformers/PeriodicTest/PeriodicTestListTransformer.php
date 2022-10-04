<?php


namespace App\OurEdu\Quizzes\Student\Transformers\PeriodicTest;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Quiz;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class PeriodicTestListTransformer extends TransformerAbstract
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
        $this->user = Auth::guard('api')->user()->student;
    }

    public function transform(Quiz $peiodicTest)
    {
        $transformedData = [
            'id' => (int) $peiodicTest->id,
            'quiz_type' => (string) $peiodicTest->quiz_type,
            'start_at' => (string) $peiodicTest->start_at,
            'end_at' => (string) $peiodicTest->end_at,
            'classroom_name' =>  (string) $peiodicTest->classroom->name,
            'quiz_time' =>  (string) $peiodicTest->quiz_time,
            'is_taken_and_finished' =>  (bool) $this->isQuizTaken($peiodicTest, $this->student)
        ];

        return $transformedData;
    }

    public function includeActions(Quiz $peiodicTest)
    {
        $actions = [];
        if ($peiodicTest->published_at) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.periodic-test.get.getPeriodicTest', [
                    'periodicTestId' => $peiodicTest->id
                ]),
                'label' => trans('app.View'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_PERIODIC_TEST
            ];
        }
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    private function isQuizTaken($peiodicTest, $student)
    {
        // todo : need to check student is returned by null !!
//        if ($takenQuiz = $student->quizzes()->where('quiz_id', $peiodicTest->id)->first()) {
//            return $takenQuiz->status == QuizStatusEnum::FINISHED;
//        }
        return false;
    }
}
