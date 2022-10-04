<?php

namespace App\OurEdu\LearningPerformance\Parent\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Models\AllQuizStudent;
use App\OurEdu\Quizzes\Parent\QuizzesPerformance;
use App\OurEdu\Quizzes\Quiz;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class StudentQuizTransformer extends TransformerAbstract
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

    public function transform(AllQuizStudent $quiz)
    {
        $transformedData = [
            'id' =>  Str::uuid(),
            'quiz_id' => $quiz->quiz_id ,
            'quiz_result_percentage' =>  $quiz->quiz_result_percentage,
            'quiz_type' => $quiz->quiz_type,
        ];
        return $transformedData;
    }
}
