<?php

namespace App\OurEdu\ResourceSubjectFormats\Repository\Audio;

use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;

class AudioRepository implements AudioRepositoryInterface
{

    private $audioData;

    public function __construct(AudioData $audioData)
    {
        $this->audioData = $audioData;
    }

    /**
     * @param array $data
     * @return AudioData|null
     */
    public function create(array $data): ?AudioData
    {
        return $this->audioData->create($data);
    }

    /**
     * @param int $id
     * @return AudioData|null
     */
    public function findOrFail(int $id): ?AudioData
    {
        return $this->audioData->findOrFail($id);
    }

    /**
     * @param AudioData $audioData
     * @param array $data
     * @return bool
     */
    public function update(AudioData $audioData, array $data): ?AudioData
    {
         $audioData->update($data);
        return $this->audioData->findOrFail($audioData->id);

    }

    public function media()
    {
        return $this->audioData->media();
    }


    public function getAudioDateBySubjectFormatId($resourceSubjectFormatSubjectId): ?AudioData
    {
        return $this->audioData->where('resource_subject_format_subject_id', $resourceSubjectFormatSubjectId)->first();
    }

}
