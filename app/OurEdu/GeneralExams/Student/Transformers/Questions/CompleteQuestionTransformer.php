<?php


namespace App\OurEdu\GeneralExams\Student\Transformers\Questions;

use League\Fractal\TransformerAbstract;

class CompleteQuestionTransformer extends TransformerAbstract
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

    /**
     * @return array
     */
    public function transform($question)
    {
        $transformedData = [
            'id' => $question->id,
            'question' => $question->question,
            'description' => $question->description,
        ];

        return $transformedData;
    }
}
