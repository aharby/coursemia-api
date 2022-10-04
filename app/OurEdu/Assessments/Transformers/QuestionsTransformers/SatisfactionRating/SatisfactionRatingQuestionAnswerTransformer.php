<?php


namespace App\OurEdu\Assessments\Transformers\QuestionsTransformers\SatisfactionRating;


use App\OurEdu\Assessments\Models\AssessmentAnswer;
use League\Fractal\TransformerAbstract;

class SatisfactionRatingQuestionAnswerTransformer extends TransformerAbstract
{
     /**
     * @var array
     */
    private $params;

    /**
     * SatisfactionRatingQuestionAnswerTransformer constructor.
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
                "answer_id" => (int) $selectedOption->optionable->id,
                "answer" => (string) $selectedOption->optionable->answer
            ];
            $data["selectedOptions"][] = $options;
        }

        return $data;

    }

}
