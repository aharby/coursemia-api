<?php

namespace App\OurEdu\ResourceSubjectFormats\Repository\Video;

use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;

interface VideoRepositoryInterface
{
    /**
     * @param array $data
     * @return VideoData|null
     */
    public function create(array $data): ?VideoData;


    /**
     * @param int $id
     * @return VideoData|null
     */
    public function findOrFail(int $id): ?VideoData;

    /**
     * @param VideoData $videoData
     * @param array $data
     * @return bool
     */
    public function update(VideoData $videoData, array $data): bool;

    public function media();


    public function getVideoDateBySubjectFormatId($resourceSubjectFormatSubjectId): ?VideoData;
}