<?php

namespace App\OurEdu\ResourceSubjectFormats\Repository\MultipleChoice;

use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;

interface MultipleChoiceRepositoryInterface
{
    /**
     * @param int $id
     * @return MultipleChoiceData|null
     */
    public function findOrFail(int $id): ?MultipleChoiceData;

    /**
     * @param array $data
     * @return MultipleChoiceData
     */
    public function create(array $data): MultipleChoiceData;


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
     * @return MultipleChoiceData|null
     */
    public function getMultipleChoiceDataBySubjectFormatId($resourceSubjectFormatSubjectId): ?MultipleChoiceData;

}
