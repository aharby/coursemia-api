<?php


namespace App\OurEdu\QuestionReport\UseCases\ReportQuestionReportUseCase;


interface ReportQuestionReportUseCaseInterface
{
    public function report($questionReportId , $user , $note , $due_date);

    public function generateTask($question , $user , $note , $due_date);

}
