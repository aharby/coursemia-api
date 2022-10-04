<?php


namespace App\OurEdu\Assessments\Transformers\QuestionsTransformers\EssayQuestion;


use App\OurEdu\Assessments\Models\AssessmentAnswer;
use League\Fractal\TransformerAbstract;

class EssayQuestionAnswerTransformer extends TransformerAbstract
{
    public function transform(AssessmentAnswer $answer)
    {
        return [
            "id" => $answer->id,
            "selectedOptions" => [],
            'answer_text' => $answer->answer_text,
        ];
    }
}
