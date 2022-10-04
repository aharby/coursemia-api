<?php

namespace App\OurEdu\VCRSessions\General\Transformers\QuizzesTransformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;

class EduSupervisorQuizTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
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
            'api_url' => buildScopeRoute('api.student.quizzes.get.quiz', [
                'quizId' => $quiz->id
            ]),
            'student_portal_url' => getDynamicLink(DynamicLinksEnum::STUDENT_GET_QUIZ, [
                'quiz_id' => $quiz->id,
                'portal_url' => env('STUDENT_PORTAL_URL'),
            ]),
        ];
        return $transformedData;
    }
}
