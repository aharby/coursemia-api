<?php

declare(strict_types=1);

namespace App\OurEdu\ResourceSubjectFormats\Repository\Page;


use App\OurEdu\ResourceSubjectFormats\Models\Page\PageData;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;

interface PageRepositoryInterface
{
    /**
     * @param int $id
     * @return ResourceSubjectFormatSubject|null
     */
    public function findOrFail(int $id): ?PageData;

    public function create(array $data): PageData;
    public function getPageDateBySubjectFormatId($resourceSubjectFormatSubjectId): ?PageData;


}
