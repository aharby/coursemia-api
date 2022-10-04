<?php

namespace App\OurEdu\QuestionReport\UseCases\MarkQuestionReportTaskAsDoneUseCase;

interface MarkQuestionReportTaskAsDoneUseCaseInterface
{
    public function markTaskAsDone(int $taskId);
}
