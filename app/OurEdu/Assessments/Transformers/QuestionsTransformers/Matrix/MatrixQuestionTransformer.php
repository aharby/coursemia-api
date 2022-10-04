<?php

namespace App\OurEdu\Assessments\Transformers\QuestionsTransformers\Matrix;

use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Assessments\Models\Questions\Matrix\AssessmentMatrixData;
use League\Fractal\TransformerAbstract;

class MatrixQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'columns',
        'rows',
        'answer'
    ];
    /**
     * @var array
     */
    private $params;

    /**
     * MatrixQuestionTransformer constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function transform(AssessmentMatrixData $question)
    {
        return [
            'id' => (int)$question->id,
            'question' => (string)$question->question,
            "no_of_columns" => (int)$question->number_of_columns,
            'no_of_rows' => (int)$question->number_of_rows,
        ];
    }

    public function includeColumns(AssessmentMatrixData $question)
    {
        $columns = $question->columns;

        return $this->collection(
            $columns,
            new MatrixQuestionColumnsTransformer(),
            ResourceTypesEnums::QUESTION_COLUMNS
        );
    }

    public function includeRows(AssessmentMatrixData $question)
    {
        $rows = $question->rows;

        return $this->collection(
            $rows,
            new MatrixQuestionRowsTransformer(),
            ResourceTypesEnums::QUESTION_ROWS
        );
    }

    public function includeAnswer(AssessmentMatrixData $matrixData)
    {
        $answerTransformer = null;
        $assessmentUser = $this->params['assessmentUser'] ?? null;

        if (
            isset($this->params['assessor'])
            and isset($this->params['assessee'])
            and isset($this->params['assessment'])
        ) {
            $assessor = $this->params['assessor'];
            $assessee = $this->params['assessee'];
            $assessment = $this->params['assessment'];

            $assessmentUser = AssessmentUser::query()
                ->where("assessment_id", "=", $assessment->id)
                ->where("user_id", "=", $assessor->id)
                ->where("assessee_id", "=", $assessee->id)
                ->where("is_finished", "=", false)
                ->orderByDesc("id")
                ->first();
        }

        if ($assessmentUser) {
            $assessmentQuestion = $matrixData->assessmentQuestion()->first();

            $answer = $assessmentUser->answers()
                ->where('assessment_question_id', $assessmentQuestion->id)
                ->first();

            if ($answer) {
                $answerTransformer = $this->item(
                    $answer,
                    new MatrixQuestionAnswerTransformer($this->params),
                    ResourceTypesEnums::Assessment_QUESTION_ANSWERS
                );
            }
        }

        return $answerTransformer;
    }
}
