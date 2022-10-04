<?php

declare(strict_types=1);

namespace App\OurEdu\ResourceSubjectFormats\Repository\Essay;


use App\OurEdu\ResourceSubjectFormats\Models\Essay\EssayData;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;

interface EssayRepositoryInterface
{
    /**
     * @param int $id
     * @return ResourceSubjectFormatSubject|null
     */
    public function findOrFail(int $id): ?EssayData;

    public function create(array $data): EssayData;

    public function update(array $data);

    public function createQuestion($data);

}
