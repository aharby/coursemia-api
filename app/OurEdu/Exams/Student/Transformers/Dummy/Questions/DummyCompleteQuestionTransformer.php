<?php


namespace App\OurEdu\Exams\Student\Transformers\Dummy\Questions;

use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class DummyCompleteQuestionTransformer extends TransformerAbstract
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
     * @param CompleteData $multipleChoiceData
     * @return array
     */
    public function transform($question)
    {
        $transformedData = [
            'id' => Str::uuid(),
            'question' => str_random(),
        ];

        $answers = [];

        if (isset($this->params['is_answer'])) {
            for ($i = 0 ; $i < 3 ; $i++) {
                $answersData = [
                    'id' => Str::uuid(),
                    'answer' => str_random(),
                ];

                $answers[] = $answersData;
            }


            $transformedData['answer'] = '';
        }

        if (count($answers)) {
            $transformedData['accepted_answers'] = $answers;
        }

        return $transformedData;
    }
}
