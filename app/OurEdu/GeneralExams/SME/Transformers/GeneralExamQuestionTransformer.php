<?php

namespace App\OurEdu\GeneralExams\SME\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\GeneralExams\Models\GeneralExamQuestion;
use App\OurEdu\GeneralExams\SME\Transformers\GeneralExamOptionTransformer;
use App\OurEdu\GeneralExams\SME\Transformers\SubjectFormatSubjectTransformer;

class GeneralExamQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'options',
        'subjectFormatSubject'
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
            'difficulty_level' => $question->difficultyLevel->title ?? '',
            'question' =>  (string) $question->question,
            'question_type' =>  (string) $question->question_type,
            'is_true' =>  (bool) $question->is_true,
            'general_exam_id' => (int) $question->general_exam_id,
            'subject_format_subject_id' => (int)$question->subject_format_subject_id,
        ];

        return $transformerData;
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

    public function includeOptions(GeneralExamQuestion $question)
    {
        if ($question->options->count()) {
            return $this->collection(
                $question->options,
                new GeneralExamOptionTransformer($this->params),
                ResourceTypesEnums::GENERAL_EXAM_OPTION
            );
        }
    }
}
