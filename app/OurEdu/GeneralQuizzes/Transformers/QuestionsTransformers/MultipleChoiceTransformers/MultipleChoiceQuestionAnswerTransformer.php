<?php

namespace App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\MultipleChoiceTransformers;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use League\Fractal\TransformerAbstract;

class MultipleChoiceQuestionAnswerTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    private $params;

    /**
     * MultipleChoiceQuestionAnswerTransformer constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function transform(GeneralQuizStudentAnswer $answer)
    {
        $answerDetails = $answer->details;

        $data = [
            'id' => (int)$answer->id,
        ];

        if (isset($this->params["show_if_is_correct"]) and $this->params["show_if_is_correct"] == true) {
            $data["is_correct"] = (bool)$answer->is_correct;
        }

        if ($answer->optionable) {
            $optionsData = [
                'answer_id' => (int)$answer->optionable->id,
                'answer' => (string)$answer->optionable->answer
            ];

            if (isset($this->params["show_if_is_correct"]) and $this->params["show_if_is_correct"] == true) {
                $optionsData["is_correct"] = (bool)$answer->is_correct;
            }

            $data["selectedOptions"][] = $optionsData;
        }

        if (isset($answerDetails)) {
            foreach ($answerDetails as $detail) {
                $optionsData = [
                    'answer_id' => (int)$detail->optionable->id,
                    'answer' => (string)$detail->optionable->answer
                ];

                if (isset($this->params["show_if_is_correct"]) and $this->params["show_if_is_correct"] == true) {
                    $optionsData["is_correct"] = (bool)$detail->is_correct_answer;
                }

                $data["selectedOptions"][] = $optionsData;
            }
        }

        return $data;
    }
}
