<?php


namespace App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers;


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

class HomeworkPreviewLikeStudentTransformer extends TransformerAbstract
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

    public function transform(GeneralQuiz $homework)
    {
        $transformedData = [
            'id' => (int)$homework->id,
            'title' => (string)$homework->title,
        ];
        return $transformedData;
    }

    public function includeActions(GeneralQuiz $homework)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.general-quizzes.homework.instructor.put.edit', ['homeworkId' => $homework->id]),
            'label' => trans('general_quizzes.edit Homework'),
            'method' => 'PUT',
            'key' => APIActionsEnums::EDIT_HOMEWORK
        ];

        if (!$homework->published_at) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.general-quizzes.homework.instructor.post.publish', ['homework' => $homework->id]),
                'label' => trans('app.Publish'),
                'method' => 'POST',
                'key' => APIActionsEnums::PUBLISH_HOMEWORK
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeQuestions (GeneralQuiz $homework) {
        $questions = $homework->questions()->paginate(env('PAGE_LIMIT', 20));
        return $this->collection($questions, new QuestionBankTransformer($homework), ResourceTypesEnums::HOMEWORK_QUESTION);
    }

}
