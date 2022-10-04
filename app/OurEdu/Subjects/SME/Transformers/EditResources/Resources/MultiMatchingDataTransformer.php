<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources\Resources;


use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;
use League\Fractal\TransformerAbstract;

class MultiMatchingDataTransformer extends TransformerAbstract
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
     * @param MultiMatchingData $multiMatchingData
     * @return array
     */
    public function transform(MultiMatchingData $multiMatchingData)
    {



        $questions = [];
        $options = [];
        foreach ($multiMatchingData->questions as $question) {
            $questions[] = [
                'id' => $question->id,
                'text' => $question->text,
                'media'=> (object) questionMedia($question),
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null,
                'audio' => (object)questionAudio($question),
                'video' => (object)questionVideo($question),
            ];

        }

        foreach ($multiMatchingData->options as $option) {
            $options[] = [
                'id' => $option->id,
                'option' => $option->option,
                'questions' => $option->questions()->pluck('res_multi_matching_questions.id')->toArray()
            ];

        }
        return [
            'id' => $multiMatchingData->id,
            'description' => $multiMatchingData->description,
            'question_feedback' => $multiMatchingData->question_feedback,
            'questions' => $questions ,
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

