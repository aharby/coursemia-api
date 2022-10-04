<?php

namespace App\OurEdu\GeneralExamReport\SME\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralExamReport\Models\GeneralExamReportQuestion;
use App\OurEdu\GeneralExamReport\SME\Transformers\Questions\QuestionDragDropTransformer;
use App\OurEdu\GeneralExamReport\SME\Transformers\Questions\QuestionHotspotTransformer;
use App\OurEdu\GeneralExamReport\SME\Transformers\Questions\QuestionMatchingTransformer;
use App\OurEdu\GeneralExamReport\SME\Transformers\Questions\QuestionMultiMatchingTransformer;
use App\OurEdu\GeneralExamReport\SME\Transformers\Questions\QuestionMultipleChoiceTransformer;
use App\OurEdu\GeneralExamReport\SME\Transformers\Questions\QuestionTrueFalseTransformer;
use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use League\Fractal\TransformerAbstract;

class GeneralExamReportQuestionsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
        'questionData'
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(GeneralExamReportQuestion $question)
    {
        $question->load('generalExamQuestion');
        $transformerData = [
            'id' => (int) $question->id,
            'total_answers' => $question->total_answers,
            'correct_answers' => $question->correct_answers,
            'wrong_answers' => $question->wrong_answers,
            'difficulty_parameter' => $question->difficulty_parameter,
            'easy_parameter' => $question->easy_parameter,
            'stability_parameter' => $question->stability_parameter,
            'trust_parameter' => $question->trust_parameter,
            'preference_parameter' => $question->preference_parameter,
            'general_exam_id' => $question->general_exam_id,
            'general_exam_report_id' => $question->general_exam_report_id,
            'question_type' =>         $question->generalExamQuestion->question_type ??'',
            'question_description' =>         $question->generalExamQuestion->description ??'',

        ];

        return $transformerData;
    }

    public function includeActions(GeneralExamReportQuestion $question)
    {
        $actions = [];
        if (!isset($this->params['view_details'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.general_exams_reports.subjectReportedQuestionDetails', ['report' => $question->id]),
                'label' => trans('app.View Question Details'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_QUESTION
            ];
        }
        if (count($actions) > 0)
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);

    }

    public function includeQuestionData(GeneralExamReportQuestion $question) {

        $generalExamQuestion = $question->generalExamQuestion;

        $generalExamQuestion->report_id = $question->id;

        $generalExamQuestion->is_ignored = $question->is_ignored;
        $generalExamQuestion->is_reported = $question->is_reported;
         switch ($generalExamQuestion->question_type) {

             case LearningResourcesEnums::TRUE_FALSE:
                 return $this->item(
                     $generalExamQuestion,
                     new QuestionTrueFalseTransformer(),
                     ResourceTypesEnums::QUESTION_EXAM_DATA
                 );
                 break;
             case ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT:
                 return $this->item(
                     $generalExamQuestion,
                     new QuestionTrueFalseTransformer(),
                     ResourceTypesEnums::QUESTION_EXAM_DATA
                 );
                 break;
             case LearningResourcesEnums::MULTI_CHOICE:

                 return $this->item(
                     $generalExamQuestion,
                     new QuestionMultipleChoiceTransformer(),
                     ResourceTypesEnums::QUESTION_EXAM_DATA
                 );
                 break;

             case LearningResourcesEnums::DRAG_DROP:
                 return $this->item(
                     $generalExamQuestion,
                     new QuestionDragDropTransformer(),
                     ResourceTypesEnums::QUESTION_EXAM_DATA
                 );
                 break;
             case LearningResourcesEnums::MATCHING:

                 return $this->item(
                     $generalExamQuestion,
                     new QuestionMatchingTransformer(),
                     ResourceTypesEnums::QUESTION_EXAM_DATA
                 );
                 break;
             case LearningResourcesEnums::MULTIPLE_MATCHING:
                 return $this->item(
                     $generalExamQuestion,
                     new QuestionMultiMatchingTransformer(),
                     ResourceTypesEnums::QUESTION_EXAM_DATA
                 );
                 break;
             case LearningResourcesEnums::HOTSPOT:
                 return $this->item(
                     $generalExamQuestion,
                     new QuestionHotspotTransformer(),
                     ResourceTypesEnums::QUESTION_EXAM_DATA
                 );
                 break;
         }

    }

}
