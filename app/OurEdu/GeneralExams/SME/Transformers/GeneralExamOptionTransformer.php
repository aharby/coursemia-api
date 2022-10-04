<?php

namespace App\OurEdu\GeneralExams\SME\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\GeneralExams\Models\GeneralExamOption;

class GeneralExamOptionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(GeneralExamOption $option)
    {
        $transformerData = [
            'id' => (int) $option->id,
            'option' =>  (string) $option->option,
            'is_correct' =>  (bool) $option->is_correct,
            'is_main_answer' =>  (bool) $option->is_main_answer,
            'general_exam_question_id' => (int) $option->general_exam_question_id,
        ];

        return $transformerData;
    }
}
