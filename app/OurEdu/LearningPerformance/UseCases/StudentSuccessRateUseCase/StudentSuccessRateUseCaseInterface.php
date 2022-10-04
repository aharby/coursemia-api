<?php


namespace App\OurEdu\LearningPerformance\UseCases\StudentSuccessRateUseCase;

interface StudentSuccessRateUseCaseInterface
{

    public function getSuccessRateOnSubject($studentId, $subjectId);

    // speed_percentage order according to all students
    public function getStudentSpeedOrderOfSolvingExams($studentId, $subjectId);

    // subject progress percentage order according to all students
    public function getSubjectProgressPercentage($studentId, $subjectId);

    // exams count percentage according to all students
    public function getExamsCountsOrder($studentId, $subjectId);
}
