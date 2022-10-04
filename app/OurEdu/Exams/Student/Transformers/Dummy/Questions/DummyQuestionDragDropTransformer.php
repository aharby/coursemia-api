<?php


namespace App\OurEdu\Exams\Student\Transformers\Dummy\Questions;


use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class DummyQuestionDragDropTransformer extends TransformerAbstract
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
     * @param DragDropData $dragDropData
     * @return array
     */
    public function transform(DragDropData $dragDropData)
    {
        $options = [];
        $optionsIds = [];
        for ($i = 0 ; $i < 3 ; $i++) {

            $optionsIds[$i] = Str::uuid();
            $options[] = [
                'id' => $optionsIds[$i],
                'option' => str_random() ,
            ];

        }

        $questions = [];
        for ($i = 0 ; $i < 3 ; $i++) {

            $questionsData = [
                'id' => Str::uuid(),
                'question' => str_random(),
            ];
            if (isset($this->params['is_answer'])) {
                $questionsData['answers'] = $optionsIds[$i];
            }
            $questions[] = $questionsData;

        }

        return [
            'id' => Str::uuid(),
            'type' => Arr::random([ResourceOptionsSlugEnum::TEXT,
                ResourceOptionsSlugEnum::IMAGE]),            'description' => str_random(),
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

