<?php

namespace App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\DragDropTransformers;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;

class DragDropQuestionAnswerTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

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
        $data = [
            'id' => (int)$answer->id,
            'answer_text' => (string)$answer->answer_text,
            'media' => (object)questionMedia($answer->questionable),
        ];

        if ($answer->questionable) {
            $data['single_question_id'] = (int)$answer->questionable->id;
        }

        if ($answer->optionable) {
            $data['answer_id'] = (int)$answer->optionable->id;
            $data['answer'] = (string)$answer->optionable->answer;
        }

        if (isset($this->params["show_if_is_correct"]) and $this->params["show_if_is_correct"] == true) {
            $data["is_correct"] = (bool)$answer->is_correct;
        }

        $answerDetails = $answer->details;
        foreach ($answerDetails as $detail) {
            $optionsData = [];

            if ($detail->questionable) {
                $optionsData['single_question_id'] = (int)$detail->questionable->id;
            }

            if ($detail->optionable) {
                $optionsData['answer_id'] = (int)$detail->optionable->id;
                $optionsData['answer'] = (string)$detail->optionable->answer;
            }

            if (isset($this->params["show_if_is_correct"]) and $this->params["show_if_is_correct"] == true) {
                $optionsData["is_correct"] = (bool)$detail->is_correct_answer;
            }

            if (count($optionsData)) {
                $data["selectedOptions"][] = $optionsData;
            }
        }

        return $data;
    }
}
