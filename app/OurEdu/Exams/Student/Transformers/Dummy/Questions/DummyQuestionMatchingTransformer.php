<?php


namespace App\OurEdu\Exams\Student\Transformers\Dummy\Questions;


use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class DummyQuestionMatchingTransformer extends TransformerAbstract
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
     * @param MatchingData $matchingData
     * @return array
     */
    public function transform(MatchingData $matchingData)
    {
        $questions = [];
        $options = [];
        $questionIds = [];
        for ($i = 0 ; $i < 3 ; $i++) {
            $questionIds[$i] = Str::uuid();
            $questions[] = [
                'id' => $questionIds[$i],
                'text' => str_random() ,
            ];

        }

        for ($i = 0 ; $i < 3 ; $i++) {

            $optionsData = [
                'id' => Str::uuid(),
                'option' => str_random() ,
            ];

            if (isset($this->params['is_answer'])) {
                $optionsData['question_id'] = $questionIds[$i];
            }
            $options[] = $optionsData;
        }
        return [
            'id' => Str::uuid(),
            'description' => str_random(),
            'questions' => $questions,
            'options' => $options
        ];
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


}

