<?php

namespace App\OurEdu\ResourceSubjectFormats\Repository\Flash;

use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;

class FlashRepository implements FlashRepositoryInterface
{

    private $flashData;

    public function __construct(FlashData $flashData)
    {
        $this->flashData = $flashData;
    }

    /**
     * @param array $data
     * @return flashData|null
     */
    public function create(array $data): ?flashData
    {
        return $this->flashData->create($data);
    }

    /**
     * @param int $id
     * @return flashData|null
     */
    public function findOrFail(int $id): ?flashData
    {
        return $this->flashData->findOrFail($id);
    }

    /**
     * @param flashData $flashData
     * @param array $data
     * @return bool
     */
    public function update(flashData $flashData, array $data): ?flashData
    {
        $flashData->update($data);
        return $this->flashData->findOrFail($flashData->id);

    }

    public function media()
    {
        return $this->flashData->media();
    }


    public function getFlashDataBySubjectFormatId($resourceSubjectFormatSubjectId): ?flashData
    {
        return $this->flashData->where('resource_subject_format_subject_id', $resourceSubjectFormatSubjectId)->first();
    }

}
