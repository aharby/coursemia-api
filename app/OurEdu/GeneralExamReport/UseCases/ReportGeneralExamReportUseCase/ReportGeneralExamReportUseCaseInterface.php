<?php


namespace App\OurEdu\GeneralExamReport\UseCases\ReportGeneralExamReportUseCase;


interface ReportGeneralExamReportUseCaseInterface
{
    public function report($questionReportId , $user , $note , $due_date);

    public function generateTask($question , $user , $note , $due_date);

}
