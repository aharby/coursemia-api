<?php

namespace App\OurEdu\ResourceSubjectFormats\Repository\Picture;

use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureData;

class PictureRepository implements PictureRepositoryInterface
{

    private $pictureData;

    public function __construct(PictureData $pictureData)
    {
        $this->pictureData = $pictureData;
    }

    /**
     * @param array $data
     * @return PictureData|null
     */
    public function create(array $data): ?PictureData
    {
        return $this->pictureData->create($data);
    }

    /**
     * @param int $id
     * @return PictureData|null
     */
    public function findOrFail(int $id): ?PictureData
    {
        return $this->pictureData->findOrFail($id);
    }

    /**
     * @param PictureData $pictureData
     * @param array $data
     * @return bool
     */
    public function update(PictureData $pictureData, array $data): ?PictureData
    {
        $pictureData->update($data);
        return $this->pictureData->findOrFail($pictureData->id);

    }

    public function media()
    {
        return $this->pictureData->media();
    }


    public function getPictureDateBySubjectFormatId($resourceSubjectFormatSubjectId): ?PictureData
    {
        return $this->pictureData->where('resource_subject_format_subject_id', $resourceSubjectFormatSubjectId)->first();
    }

    public function updateMedia($mediaId, $date)
    {
        return $this->pictureData->media()->where('id', $mediaId)->update($date);
    }
}
