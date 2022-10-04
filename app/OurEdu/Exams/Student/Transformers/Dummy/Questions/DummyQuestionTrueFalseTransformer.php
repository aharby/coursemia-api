<?php


namespace App\OurEdu\Exams\Student\Transformers\Dummy\Questions;

use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class DummyQuestionTrueFalseTransformer extends TransformerAbstract
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
     * @param TrueFalseData $trueFalseData
     * @return array
     */
    public function transform($question)
    {
        $options = [];
        for ($i = 0 ; $i < 3 ; $i++) {
            $optionData =
                [
                    'id' => Str::uuid(),
                    'option' => str_random() ,
                ];

            if (isset($this->params['is_answer'])) {
                $optionData['is_correct'] = (bool)random_int(0 , 1);
            }

            $options[] = $optionData;
        }

        $transformedData = [
            'id' => Str::uuid(),
            'type' => Arr::random([ResourceOptionsSlugEnum::TRUE_FALSE,
                ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT]),
            'text' =>  str_random() ,
        ];

        if (count($options)) {
            $transformedData['options'] = $options;
        }

        if (isset($this->params['is_answer'])) {
            $transformedData['is_true'] = (bool)random_int(0 , 1);
        }

        return $transformedData;
    }
}
