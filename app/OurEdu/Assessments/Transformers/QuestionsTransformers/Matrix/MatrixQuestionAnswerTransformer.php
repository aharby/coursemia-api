<?php


namespace App\OurEdu\Assessments\Transformers\QuestionsTransformers\Matrix;


use App\OurEdu\Assessments\Models\AssessmentAnswer;
use League\Fractal\TransformerAbstract;

class MatrixQuestionAnswerTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    private $params;

    /**
     * MatrixQuestionAnswerTransformer constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }
    public function transform(AssessmentAnswer $answer)
    {
        $data = [
            "id" => $answer->id,
            "selectedOptions" => [],
            "score" => $answer->score,
            'question_score'=>(string)$answer->score.'/'.$this->params['question_grade']
        ];

        $answerDetails = $answer->details()->get();
        foreach ($answerDetails as $selectedOption) {
            $options = [
                "row_id" => (int) $selectedOption->questionable->id,
                "col_id" => (int) $selectedOption->optionable->id,
            ];
            $data["selectedOptions"][] = $options;
        }

        return $data;

    }

}
