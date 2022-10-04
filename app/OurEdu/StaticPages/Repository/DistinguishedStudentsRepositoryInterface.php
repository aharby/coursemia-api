<?php


namespace App\OurEdu\StaticPages\Repository;


use App\OurEdu\StaticPages\Enums\DistinguishedStudentsEnum;

interface DistinguishedStudentsRepositoryInterface
{
    public function listDistinguishedStudentsInDays($days = DistinguishedStudentsEnum::EXAM_DATE_LIMIT_IN_DAYS);
}
