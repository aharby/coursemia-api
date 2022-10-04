<?php

namespace App\OurEdu\ResourceSubjectFormats\Repository\Flash;

use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;

interface FlashRepositoryInterface
{
    /**
     * @param array $data
     * @return FlashData|null
     */
    public function create(array $data): ?FlashData;


    /**
     * @param int $id
     * @return FlashData|null
     */
    public function findOrFail(int $id): ?FlashData;

    /**
     * @param FlashData $flashData
     * @param array $data
     * @return bool
     */
    public function update(FlashData $flashData, array $data): ?FlashData;

    public function media();


    public function getFlashDataBySubjectFormatId($resourceSubjectFormatSubjectId): ?FlashData;
}
