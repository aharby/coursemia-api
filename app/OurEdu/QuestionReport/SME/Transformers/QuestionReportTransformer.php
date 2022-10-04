<?php


namespace App\OurEdu\QuestionReport\SME\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\QuestionReport\SME\Transformers\Questions\QuestionCompleteTransformer;
use App\OurEdu\QuestionReport\SME\Transformers\Questions\QuestionDragDropTransformer;
use App\OurEdu\QuestionReport\SME\Transformers\Questions\QuestionMatchingTransformer;
use App\OurEdu\QuestionReport\SME\Transformers\Questions\QuestionMultiMatchingTransformer;
use App\OurEdu\QuestionReport\SME\Transformers\Questions\QuestionMultipleChoiceTransformer;
use App\OurEdu\QuestionReport\SME\Transformers\Questions\QuestionTrueFalseTransformer;
use League\Fractal\TransformerAbstract;

class QuestionReportTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
        'questionData',
    ];

    protected $filter;

    public function __construct(array $filter = [])
    {
        $this->filter = $filter;
    }

    /**
     * @param $questionReport
     * @return array
     */
    public function transform($questionReport)
    {
        return [
            'id' => $questionReport->id,
            'total_answer' => $questionReport->total_answer,
            'correct_answer' => $questionReport->correct_answer,
            'slug' => $questionReport->slug,
            'question_type' => (string) $questionReport->question_type,
            'header' => $questionReport->header,
            'difficulty_level' => trans('difficulty_levels.'.$questionReport->difficulty_level),
            'difficulty_level_result_equation' => $questionReport->difficulty_level_result_equation,
            'subject_format_subject' => $questionReport->subjectFormatSubject->title,
        ];
    }

    public function includeQuestionData($questionReport)
    {
        switch ($questionReport->slug) {
            case LearningResourcesEnums::TRUE_FALSE:
                return $this->item(
                    $questionReport,
                    new QuestionTrueFalseTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;
            case LearningResourcesEnums::MULTI_CHOICE:

                return $this->item(
                    $questionReport,
                    new QuestionMultipleChoiceTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;

            case LearningResourcesEnums::DRAG_DROP:
                return $this->item(
                    $questionReport,
                    new QuestionDragDropTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;
            case LearningResourcesEnums::MATCHING:

                return $this->item(
                    $questionReport,
                    new QuestionMatchingTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;
            case LearningResourcesEnums::MULTIPLE_MATCHING:
                return $this->item(
                    $questionReport,
                    new QuestionMultiMatchingTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;
            case LearningResourcesEnums::COMPLETE:
                return $this->item(
                    $questionReport,
                    new QuestionCompleteTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;
        }
    }

    public function includeActions($questionReport)
    {
        $actions = [];
        if (!isset($this->filter['view_question'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.sme.question.report.get.view.Question',
                    ['questionId' => $questionReport->id]
                ),
                'label' => trans('question_reports.View Question'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_QUESTION
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
