<?php


namespace App\OurEdu\Exams\Student\Transformers\Dummy\Questions;


use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class DummyQuestionMultipleChoiceTransformer extends TransformerAbstract
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
        for ($i = 0 ; $i < 3 ; $i++) {
            $optionsData = [
                'id' => Str::uuid(),
                'option' => str_random() ,
            ];

            if (isset($this->params['is_answer'])) {
                $optionsData['is_correct_answer'] = (bool)random_int(0 , 1);
            }
            $options[] = $optionsData;
        }
        $questions = [
            'id' => Str::uuid(),
            'type' => Arr::random([ResourceOptionsSlugEnum::MULTIPLE_CHOICE_SLUG_SINGLE_CHOICE,
                ResourceOptionsSlugEnum::MULTIPLE_CHOICE_SLUG_MULTIPLE_CHOICE]),
            'question' => str_random() ,
            'options' => $options,
        ];

        return $questions;
    }


}

