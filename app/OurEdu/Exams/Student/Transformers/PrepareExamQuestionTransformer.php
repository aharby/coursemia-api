<?php

namespace App\OurEdu\Exams\Student\Transformers;

use App\OurEdu\Exams\Student\Transformers\Questions\HotspotQuestionTransformer;
use App\OurEdu\Reports\ReportEnum;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Exams\Models\ExamQuestion;
use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Exams\Student\Transformers\ExamTransformer;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Transformers\TrueFalseDataTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\CompleteQuestionTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionDragDropTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionTrueFalseTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionMultiMatchingTransformer;
use App\OurEdu\Exams\Student\Transformers\Questions\QuestionMultipleChoiceTransformer;

class PrepareExamQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questionData',
    ];

    protected array $availableIncludes = [
    ];

    protected $questionsArray;
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(ExamQuestion $question)
    {
        $transformerData = [
            'id' => (int) $question->id,
            'difficulty_level' => trans('difficulty_levels.'.$question->difficulty_level),
            'question_type' =>  $question->question_type,
            'subject_id' => (int)$question->subject_id,
            'subject_format_subject_id' => (int)$question->subject_format_subject_id,
            'time_to_solve' => $question->time_to_solve,
        ];


        return $transformerData;
    }

    public function includeQuestionData(ExamQuestion $question)
    {
        $data = $question->question()->get()->first();

        switch ($question->question_type) {

            case LearningResourcesEnums::TRUE_FALSE:
                return $this->item(
                    $data,
                    new QuestionTrueFalseTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::MULTI_CHOICE:
                return $this->item(
                    $data,
                    new QuestionMultipleChoiceTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::DRAG_DROP:
                return $this->item(
                    $data,
                    new QuestionDragDropTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::MATCHING:

                return $this->item(
                    $data,
                    new QuestionMatchingTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::MULTIPLE_MATCHING:
                return $this->item(
                    $data,
                    new QuestionMultiMatchingTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::COMPLETE:
                return $this->item(
                    $data,
                    new CompleteQuestionTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;

            case LearningResourcesEnums::HOTSPOT:
                return $this->item(
                    $data,
                    new HotspotQuestionTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;
        }
    }
}
