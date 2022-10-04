<?php


namespace App\OurEdu\Quizzes\Transformers\HomeWork;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizStatusEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Models\StudentQuiz;
use League\Fractal\TransformerAbstract;

class HomeWorkStudentListTransformer extends TransformerAbstract
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

    public function transform(StudentQuiz $studentQuiz)
    {
        $transformedData = [
            'id' => (int) $studentQuiz->id,
            'student_name' => (string) $studentQuiz->student->user->name,
            'student_id' => (string) $studentQuiz->student->id,
            'quiz_type' => (string) $studentQuiz->quiz->quiz_type,
        ];

        if ($studentQuiz->status == QuizStatusEnum::FINISHED){
            $transformedData['quiz_result_percentage'] =  $studentQuiz->quiz_result_percentage;
        }

        return $transformedData;
    }

    public function includeActions(StudentQuiz $studentQuiz)
    {
        $actions = [];
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.homework.get.student.homework', [
                'homeworkId' => $studentQuiz->quiz_id,
                'studentId' => $studentQuiz->student_id
            ]),
            'label' => trans('quiz.view'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_STUDENT_QUIZ
        ];
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
