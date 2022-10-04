<?php


namespace App\OurEdu\GeneralExams\Student\Transformers\Questions;


use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use League\Fractal\TransformerAbstract;

class QuestionMatchingTransformer extends TransformerAbstract
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

    public function transform($matchingData)
    {
        $questions = [];
        $options = [];
        foreach ($matchingData->questions as $question) {
            $questions[] = [
                'id' => $question->id,
                'text' => $question->question,
                'media'=> (object) questionMedia($question)
            ];
        }
        foreach ($matchingData->options as $option) {

            $optionsData = [
                'id' => $option->id,
                'option' => $option->option,
            ];

            $options[] = $optionsData;
        }


        return [
            'id' => $matchingData->id,
            'description' => $matchingData->description,
            'questions' => $questions,
            'options' => $options
        ];
    }

}

