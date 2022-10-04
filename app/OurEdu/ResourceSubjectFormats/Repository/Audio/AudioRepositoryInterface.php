<?php

namespace App\OurEdu\ResourceSubjectFormats\Repository\Audio;

use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;

interface AudioRepositoryInterface
{
    /**
     * @param array $data
     * @return AudioData|null
     */
    public function create(array $data): ?AudioData;


    /**
     * @param int $id
     * @return AudioData|null
     */
    public function findOrFail(int $id): ?AudioData;

    /**
     * @param AudioData $audioData
     * @param array $data
     * @return bool
     */
    public function update(AudioData $audioData, array $data): ?AudioData;

    public function media();


    public function getAudioDateBySubjectFormatId($resourceSubjectFormatSubjectId): ?AudioData;
}
