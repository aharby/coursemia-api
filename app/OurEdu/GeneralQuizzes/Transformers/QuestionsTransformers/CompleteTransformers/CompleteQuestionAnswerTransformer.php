<?php

namespace App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\CompleteTransformers;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use League\Fractal\TransformerAbstract;

class CompleteQuestionAnswerTransformer extends TransformerAbstract
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
        ];


        if (isset($this->params["show_if_is_correct"]) and $this->params["show_if_is_correct"] == true) {
            $data["is_correct"] = (bool)$answer->is_correct;
            $data['score'] = (float)$answer->score;
        }


        return $data;
    }
}
