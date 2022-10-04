<?php


namespace App\OurEdu\ResourceSubjectFormats\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;
use League\Fractal\TransformerAbstract;

class AudioDataTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'audioDataMedia'
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
     * @param AudioData $audioData
     * @return array
     */
    public function transform(AudioData $audioData)
    {
        return [
            'id' => $audioData->id,
            'title' => $audioData->title,
            'description' => $audioData->description,
            'audio' => $audioData->audio_type == 'url' ? $audioData->link : null,
            'audio_type' => $audioData->audio_type

        ];
    }

    public function includeAudioDataMedia(AudioData $audioData)
    {
        if ($audioData->media()->exists()) {
            return $this->item($audioData->media()->first(),new AudioDataMediaTransformer(),ResourceTypesEnums::RESOURCE_DATA_MEDIA);
        }
    }


}

