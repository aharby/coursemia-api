<?php


namespace App\OurEdu\GeneralExams\Student\Transformers\Questions;


use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use League\Fractal\TransformerAbstract;

class QuestionMultiMatchingTransformer extends TransformerAbstract
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


    public function transform($multiMatchingData)
    {


        $questions = [];
        $options = [];
        foreach ($multiMatchingData->questions as $question) {
            $questions[] = [
                'id' => $question->id,
                'text' => $question->question,
                'media'=> (object) questionMedia($question),
            ];

        }

        foreach ($multiMatchingData->multiMatchingOptions as $option) {
            $optionsData = [
                'id' => $option->id,
                'option' => $option->option
            ];

            $options[] = $optionsData;
        }


        return [
            'id' => $multiMatchingData->id,
            'description' => $multiMatchingData->description,
            'questions' => $questions,
            'options' => $options
        ];
    }



}

