<?php


namespace App\OurEdu\Exams\Student\Transformers\Competitions\Events;

use App\OurEdu\Exams\Student\Transformers\Competitions\Questions\CompetitionQuestionHotspotTransformer;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionDragDropTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionTrueFalseTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionMultiMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionMultipleChoiceTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\CompetitionQuestionCompleteTransformer;
use App\OurEdu\Exams\Student\Transformers\Competitions\Questions\CompetitionQuestionDragDropTransformer;
use App\OurEdu\Exams\Student\Transformers\Competitions\Questions\CompetitionQuestionMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\Competitions\Questions\CompetitionQuestionTrueFalseTransformer;
use App\OurEdu\Exams\Student\Transformers\Competitions\Questions\CompetitionQuestionMultiMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\Competitions\Questions\CompetitionQuestionMultipleChoiceTransformer;

class CompetitionQuestionAnsweredTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];
    protected array $availableIncludes = [
    ];
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform($data)
    {
        $transformerData = [
            'id' => Str::uuid(),
            'competitionId' => $data['competitionId'],
            'questionId' => $data['questionId'],
            'questionCorrectRatio' => $data['questionCorrectRatio'],
            'questionNotCorrectRatio' => $data['questionNotCorrectRatio'],
        ];

        return $transformerData;
    }
}
