<?php

namespace App\OurEdu\ResourceSubjectFormats\Repository\Picture;

use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureData;

interface PictureRepositoryInterface
{
    /**
     * @param array $data
     * @return PictureData|null
     */
    public function create(array $data): ?PictureData;


    /**
     * @param int $id
     * @return PictureData|null
     */
    public function findOrFail(int $id): ?PictureData;

    /**
     * @param PictureData $pictureData
     * @param array $data
     * @return bool
     */
    public function update(PictureData $pictureData, array $data): ?PictureData;

    public function media();


    public function getPictureDateBySubjectFormatId($resourceSubjectFormatSubjectId): ?PictureData;
}
