<?php

namespace App\OurEdu\GeneralExams\Student\Transformers;

use App\OurEdu\GeneralExams\Student\Transformers\Questions\CompleteQuestionTransformer;
use App\OurEdu\GeneralExams\Student\Transformers\Questions\QuestionDragDropTransformer;
use App\OurEdu\GeneralExams\Student\Transformers\Questions\QuestionMatchingTransformer;
use App\OurEdu\GeneralExams\Student\Transformers\Questions\QuestionMultiMatchingTransformer;
use App\OurEdu\GeneralExams\Student\Transformers\Questions\QuestionMultipleChoiceTransformer;
use App\OurEdu\GeneralExams\Student\Transformers\Questions\QuestionTrueFalseTransformer;
use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\GeneralExams\Models\GeneralExamQuestion;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\GeneralExams\Student\Transformers\GeneralExamTransformer;
use App\OurEdu\GeneralExams\Student\Transformers\GeneralExamOptionTransformer;

class GeneralExamQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questionData'
    ];

    protected array $availableIncludes = [
        'exam',
        'actions',
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(GeneralExamQuestion $question)
    {
        $transformerData = [
            'id' => (int) $question->id,
            'question_type' =>  (string) $question->question_type,
            'difficulty_level' => $question->difficultyLevel->title ?? '',
//            'question' =>  (string) $question->question,
            'general_exam_id' => (int) $question->general_exam_id,
        ];

        return $transformerData;
    }

//    public function includeOptions(GeneralExamQuestion $question)
//    {
//        if (in_array($question->question_type, [LearningResourcesEnums::COMPLETE, LearningResourcesEnums::HOTSPOT])) {
//            return;
//        }
//
//        if ($question->options->count()) {
//            return $this->collection(
//                $question->options,
//                new GeneralExamOptionTransformer($this->params),
//                ResourceTypesEnums::GENERAL_EXAM_OPTION
//            );
//        }
//    }

    public function includeExam(GeneralExamQuestion $question)
    {
        if ($question->exam) {
            return $this->item(
                $question->exam,
                new GeneralExamTransformer($this->params),
                ResourceTypesEnums::GENERAL_EXAM
            );
        }
    }

    public function includeActions(GeneralExamQuestion $question)
    {
        if (isset($this->params['next'])) {
            $actions[] = [
                'endpoint_url' => $this->params['next'],
                'label' => trans('exam.Next Question'),
                'method' => 'GET',
                'key' => APIActionsEnums::NEXT_QUESTION
            ];
        }

        if (isset($this->params['previous'])) {
            $actions[] = [
                'endpoint_url' => $this->params['previous'],
                'label' => trans('exam.Previous Question'),
                'method' => 'GET',
                'key' => APIActionsEnums::PREVIOUS_QUESTION
            ];
        }

        $page = request()->input('page') ?? 1;

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.student.general_exams.post.answer',
                ['examId' => $question->general_exam_id, 'page' => $page]
            ),
            'label' => trans('exam.Post answer'),
            'method' => 'POST',
            'key' => APIActionsEnums::POST_ANSWER
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.student.general_exams.post.finish',
                ['examId' => $question->general_exam_id, 'page' => $page]
            ),
            'label' => trans('exam.Finish Exam'),
            'method' => 'POST',
            'key' => APIActionsEnums::FINISH_EXAM
        ];

        if (count($actions)) {
            return $this->collection(
                $actions,
                new ActionTransformer($this->params),
                ResourceTypesEnums::ACTION
            );
        }
    }


    public function includeQuestionData(GeneralExamQuestion $question)
    {

        switch ($question->question_type) {

            case ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT:
            case LearningResourcesEnums::TRUE_FALSE:
                return $this->item(
                    $question,
                    new QuestionTrueFalseTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;
            case ResourceOptionsSlugEnum::MULTIPLE_CHOICE_SLUG_SINGLE_CHOICE:
            case LearningResourcesEnums::MULTI_CHOICE:
                return $this->item(
                    $question,
                    new QuestionMultipleChoiceTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::DRAG_DROP:
                return $this->item(
                    $question,
                    new QuestionDragDropTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::MATCHING:

                return $this->item(
                    $question,
                    new QuestionMatchingTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::MULTIPLE_MATCHING:
                return $this->item(
                    $question,
                    new QuestionMultiMatchingTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::COMPLETE:
                return $this->item(
                    $question,
                    new CompleteQuestionTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;
        }
    }

}
