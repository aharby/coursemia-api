<?php


namespace App\OurEdu\GeneralExams\Student\Transformers\Questions;

use League\Fractal\TransformerAbstract;

class QuestionTrueFalseTransformer extends TransformerAbstract
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
        $options = [];
        foreach ($question->options as $option) {
            $optionData =
                [
                    'id' => $option->id,
                    'option' => $option->option,
                ];
            $options[] = $optionData;
        }

        $transformedData = [
            'id' => $question->id,
            'type' => $question->question_type,
            'question_type' => $question->question_type,
            'text' => $question->question,
            'media' => (object) questionMedia($question),
            'description' => $question->description,
        ];


        if (count($options)) {
            $transformedData['options'] = $options;
        }

        if (isset($this->params['is_answer'])) {
            $transformedData['is_true'] = (bool) $question->is_true;
        }

        return $transformedData;
    }
}
