<?php


namespace App\OurEdu\GeneralExamReport\Repository;


use App\OurEdu\GeneralExamReport\Models\GeneralExamReportTask;

interface GeneralExamReportTaskRepositoryInterface
{

    public function create(array $data): GeneralExamReportTask;
}
