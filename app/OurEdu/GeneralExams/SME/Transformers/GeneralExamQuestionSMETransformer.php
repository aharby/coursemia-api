<?php

namespace App\OurEdu\GeneralExams\SME\Transformers;

use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\GeneralExams\Models\GeneralExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\GeneralExams\SME\Transformers\Questions\CompleteDataTransformer;
use App\OurEdu\GeneralExams\SME\Transformers\Questions\DragDropDataTransformer;
use App\OurEdu\GeneralExams\SME\Transformers\Questions\HotspotDataTransformer;
use App\OurEdu\GeneralExams\SME\Transformers\Questions\MatchingDataTransformer;
use App\OurEdu\GeneralExams\SME\Transformers\Questions\MultiMatchingDataTransformer;
use App\OurEdu\GeneralExams\SME\Transformers\Questions\MultipleChoiceDataTransformer;
use App\OurEdu\GeneralExams\SME\Transformers\Questions\TrueFalseDataTransformer;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;

class GeneralExamQuestionSMETransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questionData',
    ];

    protected array $availableIncludes = [
        'subjectFormatSubject'
    ];

    protected $questionsArray;
    private $params;
    private $exam;

    public function __construct(GeneralExam $exam, $params = [])
    {
        $this->params = $params;
        $this->exam = $exam;
    }

    public function transform(GeneralExamQuestion $question)
    {
        $transformerData = [
            'id' => (int)$question->id,
            'difficulty_level' => $question->difficultyLevel->title ?? '',
            'question_type' => $question->question_type,
            'subject_id' => (int)$question->subject_id,
            'subject_format_subject_id' => (int)$question->subject_format_subject_id,
        ];

        return $transformerData;
    }

    public function includeQuestionData(GeneralExamQuestion $question)
    {
        $data = $question->questionable;
        $this->params['questionId'] = $question->questionable->id;

        switch ($question->question_type) {
            case LearningResourcesEnums::TRUE_FALSE:
                return $this->item(
                    $data->parentData,
                    new TrueFalseDataTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::MULTI_CHOICE:
                return $this->item(
                    $data->parentData,
                    new MultipleChoiceDataTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::DRAG_DROP:
                return $this->item(
                    $data,
                    new DragDropDataTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::MATCHING:
                return $this->item(
                    $data,
                    new MatchingDataTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::MULTIPLE_MATCHING:
                return $this->item(
                    $data,
                    new MultiMatchingDataTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );

                break;

            case LearningResourcesEnums::COMPLETE:
                return $this->item(
                    $data->parentData,
                    new CompleteDataTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;

            case LearningResourcesEnums::HOTSPOT:
                return $this->item(
                    $data->parentData,
                    new HotspotDataTransformer($this->params),
                    ResourceTypesEnums::QUESTION_EXAM_DATA
                );
                break;
        }
    }

    public function includeSubjectFormatSubject(GeneralExamQuestion $question)
    {
        if ($question->subjectFormatSubject) {
            return $this->item(
                $question->subjectFormatSubject,
                new SubjectFormatSubjectTransformer($this->params),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }
}
