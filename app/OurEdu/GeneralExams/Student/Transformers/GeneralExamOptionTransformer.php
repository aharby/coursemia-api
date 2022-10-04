<?php

namespace App\OurEdu\GeneralExams\Student\Transformers;

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
            'general_exam_question_id' => (int) $option->general_exam_question_id,
        ];

        return $transformerData;
    }
}
