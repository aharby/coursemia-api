<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources\Resources;


use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use League\Fractal\TransformerAbstract;

class MatchingDataTransformer extends TransformerAbstract
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
        foreach ($matchingData->questions as $question) {
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
        foreach ($matchingData->options as $option) {
            $options[] = [
                'id' => $option->id,
                'option' => $option->option,
                'question_id' => $option->res_matching_question_id
            ];
        }
        return [
            'id' => $matchingData->id,
            'description' => $matchingData->description,
            'question_feedback' => $matchingData->question_feedback,
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

