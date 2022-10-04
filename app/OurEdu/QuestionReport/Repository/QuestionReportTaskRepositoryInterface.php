<?php


namespace App\OurEdu\QuestionReport\Repository;


use App\OurEdu\QuestionReport\Models\QuestionReportTask;

interface QuestionReportTaskRepositoryInterface
{

    public function create(array $data): QuestionReportTask;
}
