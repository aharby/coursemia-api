<?php


namespace App\OurEdu\GeneralExams\Student\Transformers\Questions;


use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use League\Fractal\TransformerAbstract;

class QuestionMultipleChoiceTransformer extends TransformerAbstract
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

    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    /**
     * @param MultipleChoiceData $multipleChoiceData
     * @return array
     */
    public function transform($question)
    {
        $questions = [];

        $options = [];
        foreach ($question->options as $option) {
            $optionsData = [
                'id' => $option->id,
                'option' => $option->option,
            ];


            $options[] = $optionsData;
        }
        $questions = [
            'id' => $question->id,
            'type' => $question->question_type,
            'question_type' => $question->question_type,
            'question' => $question->question,
            'url' => $question->url,
            'media' => (object) questionMedia($question),
            'description' => $question->description,
            'options' => $options,
        ];

        return $questions;
    }


}

