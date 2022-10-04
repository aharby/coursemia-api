<?php

namespace App\OurEdu\ResourceSubjectFormats\Repository\Video;

use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;

class VideoRepository implements VideoRepositoryInterface
{

    private $videoData;

    public function __construct(VideoData $videoData)
    {
        $this->videoData = $videoData;
    }

    /**
     * @param array $data
     * @return VideoData|null
     */
    public function create(array $data): ?VideoData
    {
        return $this->videoData->create($data);
    }

    /**
     * @param int $id
     * @return VideoData|null
     */
    public function findOrFail(int $id): ?VideoData
    {
        return $this->videoData->findOrFail($id);
    }

    /**
     * @param VideoData $videoData
     * @param array $data
     * @return bool
     */
    public function update(VideoData $videoData, array $data): bool
    {
        return $videoData->update($data);
    }

    public function media()
    {
        return $this->videoData->media();
    }


    public function getVideoDateBySubjectFormatId($resourceSubjectFormatSubjectId): ?VideoData
    {
        return $this->videoData->where('resource_subject_format_subject_id', $resourceSubjectFormatSubjectId)->first();
    }

}