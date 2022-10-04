<?php


namespace App\OurEdu\Quizzes\Student\Transformers\PeriodicTest;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Quiz;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class PeriodicTestTransformer extends TransformerAbstract
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

    public function transform(Quiz $periodicTest)
    {
        $transformedData = [
            'id' => (int) $periodicTest->id,
            'status' => $this->getQuizStatus($periodicTest),
            'start_at' => (string) $periodicTest->start_at,
            'end_at' => (string) $periodicTest->end_at,
            'grade_class_id' => $periodicTest->grade_class_id,
            'subject_id' => $periodicTest->subject_id ,
            'periodic_test_title' => $periodicTest->quiz_title ,
        ];
        return $transformedData;
    }

    public function includeActions(Quiz $periodicTest)
    {
        $actions = [];
        // if student started the homework
        if ($studentQuiz = $this->student->quizzes()->where('quiz_id', $periodicTest->id)->first()) {
            // if student != finished the quiz => get the first question
            if ($studentQuiz->status != QuizStatusEnum::FINISHED) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.student.periodic-test.get.next-back-questions', [
                        'periodicTestId' => $periodicTest->id,
                    ]),
                    'label' => trans('app.Continue'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::CONTINUE_QUIZ
                ];
            }
        } else {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.periodic-test.get.start-periodic-test', [
                    'periodicTestId' => $periodicTest->id
                ]),
                'label' => trans('app.Start'),
                'method' => 'GET',
                'key' => APIActionsEnums::CONTINUE_PERIODIC_TEST
            ];
        }
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    private function getQuizStatus($periodicTest)
    {
        if ($studentQuiz = $this->student->quizzes()->where('quiz_id', $periodicTest->id)->first()) {
            return $studentQuiz->status;
        } else {
            return QuizStatusEnum::NOT_STARTED;
        }
    }
}
