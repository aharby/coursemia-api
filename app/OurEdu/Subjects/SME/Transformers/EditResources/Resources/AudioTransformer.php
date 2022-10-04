<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources\Resources;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;
use League\Fractal\TransformerAbstract;

class AudioTransformer extends TransformerAbstract
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
            return $this->item($audioData->media()->first(), new AudioDataMediaTransformer(), ResourceTypesEnums::RESOURCE_DATA_MEDIA);
        }
    }
}
