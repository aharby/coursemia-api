<?php


namespace App\OurEdu\VCRSchedules\UseCases\VCRNotificationUseCase;


interface VCRNotificationUseCaseInterface
{

    // send for session participants (students)
    public function examGeneratedNotification($sessionId, $examId);

    // send for session instructor per every student after finishing the exam
    public function examFinishedNotification($exam);
}
