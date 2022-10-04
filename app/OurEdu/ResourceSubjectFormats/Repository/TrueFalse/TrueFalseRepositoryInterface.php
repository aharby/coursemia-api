<?php

declare(strict_types=1);

namespace App\OurEdu\ResourceSubjectFormats\Repository\TrueFalse;


use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;

interface TrueFalseRepositoryInterface
{
    /**
     * @param int $id
     * @return ResourceSubjectFormatSubject|null
     */
    public function findOrFail(int $id): ?TrueFalseData;

    public function create(array $data): TrueFalseData;

    public function update(array $data);

    public function createQuestion($data);

    public function insertMultipleOptions($data);
}
