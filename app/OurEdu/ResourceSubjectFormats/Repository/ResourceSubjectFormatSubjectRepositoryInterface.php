<?php

declare(strict_types=1);

namespace App\OurEdu\ResourceSubjectFormats\Repository;


use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;

interface ResourceSubjectFormatSubjectRepositoryInterface
{
    /**
     * @param int $id
     * @return ResourceSubjectFormatSubject|null
     */
    public function findOrFail(int $id) : ?ResourceSubjectFormatSubject;
    public function getResourcesIdsForBySubjectId(int $id) : ?array ;
}
