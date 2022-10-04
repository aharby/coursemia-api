<?php

declare(strict_types=1);

namespace App\OurEdu\Subjects\Repository;

use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

interface SubjectFormatSubjectRepositoryInterface
{

    /**
     * @param int $id
     * @return SubjectFormatSubject|null
     */
    public function findOrFail(int $id): ?SubjectFormatSubject;

    public function filterActiveIds(array $ids): ?array;

    public function toggleActive();

    public function getSectionResources($sectionID);

    public function getSectionsByIds($sectionIds);

    public function getSectionTasks($section, $onlyNotAssigned);
}
