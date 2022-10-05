<?php


namespace App\Modules\StaticPages\Repository;


use App\Modules\StaticPages\Enums\DistinguishedStudentsEnum;

interface DistinguishedStudentsRepositoryInterface
{
    public function listDistinguishedStudentsInDays($days = DistinguishedStudentsEnum::EXAM_DATE_LIMIT_IN_DAYS);
}
