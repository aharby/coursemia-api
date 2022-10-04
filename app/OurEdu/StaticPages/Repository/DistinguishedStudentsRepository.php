<?php


namespace App\OurEdu\StaticPages\Repository;


use App\OurEdu\StaticPages\Enums\DistinguishedStudentsEnum;
use App\OurEdu\StaticPages\Models\DistinguishedStudent;
use Carbon\Carbon;

class DistinguishedStudentsRepository implements DistinguishedStudentsRepositoryInterface
{
    private $model;

    public function __construct(DistinguishedStudent $distinguishedStudent)
    {
        $this->model = $distinguishedStudent;
    }

    public function listDistinguishedStudentsInDays($days = DistinguishedStudentsEnum::EXAM_DATE_LIMIT_IN_DAYS){

        $endDate = new Carbon();
        $startDate =  (new Carbon())->subDays(DistinguishedStudentsEnum::EXAM_DATE_LIMIT_IN_DAYS);

        return $this->model
            ->where('created_at' , '>=' , $startDate)
            ->where('created_at' , '<' , $endDate)
            ->get();
    }

}
