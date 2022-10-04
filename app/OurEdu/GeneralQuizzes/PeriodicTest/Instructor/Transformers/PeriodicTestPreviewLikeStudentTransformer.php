<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Classroom\Transformers\StudentTransformer;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\QuestionBankRepository;
use Illuminate\Support\Facades\DB;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Users\Transformers\UserTransformer;

use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Transformers\ClassroomTransformer;

class PeriodicTestPreviewLikeStudentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questions',
        'actions',
    ];
    protected array $availableIncludes = [

    ];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(GeneralQuiz $periodicTest)
    {
        $transformedData = [
            'id' => (int)$periodicTest->id,
            'title' => (string)$periodicTest->title,
        ];
        return $transformedData;
    }

    public function includeActions(GeneralQuiz $periodicTest)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.general-quizzes.periodic-test.instructors.put.edit', ['periodicTestId' => $periodicTest->id]),
            'label' => trans('general_quizzes.edit Periodic Test'),
            'method' => 'PUT',
            'key' => APIActionsEnums::EDIT_PERIODIC_TEST
        ];

        if (!$periodicTest->published_at) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.general-quizzes.periodic-test.instructors.post.publish', ['periodicTest' => $periodicTest->id]),
                'label' => trans('app.Publish'),
                'method' => 'POST',
                'key' => APIActionsEnums::PUBLISH_PERIODIC_TEST
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeQuestions (GeneralQuiz $periodicTest) {
        $questions = $periodicTest->questions()->paginate(env('PAGE_LIMIT', 20));
        return $this->collection($questions, new QuestionBankTransformer($periodicTest), ResourceTypesEnums::Periodic_Test_QUESTION);
    }

}
