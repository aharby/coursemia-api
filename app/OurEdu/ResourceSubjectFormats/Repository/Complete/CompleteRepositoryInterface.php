<?php

namespace App\OurEdu\ResourceSubjectFormats\Repository\Complete;

use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;

interface CompleteRepositoryInterface
{
    /**
     * @param int $id
     * @return CompleteData|null
     */
    public function findOrFail(int $id): ?CompleteData;

    /**
     * @param array $data
     * @return CompleteData
     */
    public function create(array $data): CompleteData;


    /**
     * @param array $data
     * @return bool
     */
    public function update(array $data) :bool;

    /**
     * @param $data
     * @return mixed
     */
    public function createQuestion($data);

    /**
     * @param $resourceSubjectFormatSubjectId
     * @return CompleteData|null
     */
    public function getCompleteDataBySubjectFormatId($resourceSubjectFormatSubjectId): ?CompleteData;
}
