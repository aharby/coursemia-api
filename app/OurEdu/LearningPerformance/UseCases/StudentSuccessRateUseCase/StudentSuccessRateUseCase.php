<?php

namespace App\OurEdu\LearningPerformance\UseCases\StudentSuccessRateUseCase;

use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;

class StudentSuccessRateUseCase implements StudentSuccessRateUseCaseInterface
{
    private $examRepository;
    private $subjectRepository;

    public function __construct(
        ExamRepositoryInterface $examRepository,
        SubjectRepositoryInterface $subjectRepository
    )
    {
        $this->examRepository = $examRepository;
        $this->subjectRepository = $subjectRepository;
    }

    public function getSuccessRateOnSubject($studentId, $subjectId)
    {
        $results = $this->examRepository
            ->pluckAllStudentExamsResultsOnSubject($studentId, $subjectId);
        $percentage = 0;
        if ($results->count() > 0) {
            $percentage = ($results->sum() / $results->count());
        }
        return number_format($percentage, 2, '.', '');
    }

    // speed_percentage order according to all students
    public function getStudentSpeedOrderOfSolvingExams($studentId, $subjectId)
    {
        if(isset($studentId)) {
            $res = $this->examRepository->getStudentsSpeedPercentageOrderInSubject($subjectId);
            $order = array_search($studentId, array_keys($res));
        }

        return (is_bool($order) ? 0 : $order + 1);
    }

    // subject progress percentage order according to all students
    public function getSubjectProgressPercentage($studentId, $subjectId)
    {
        if(isset($studentId)) {
            $studentsProgressOrders = $this->subjectRepository->getAllStudentsProgress($subjectId);
            $order = array_search($studentId, array_keys($studentsProgressOrders));
        }
        
        return (is_bool($order) ? 0 : $order + 1);
    }

    // exams count percentage according to all students
    public function getExamsCountsOrder($studentId, $subjectId)
    {
        if(isset($studentId)) {
            $orders = $this->examRepository->getAllStudentsExamsCounts($subjectId);
            $order = array_search($studentId, array_keys($orders));
        }
        
        return (is_bool($order) ? 0 : $order + 1);
    }

    public function getExamCountStudent($subjectId)
    {
        return count($this->examRepository->getAllStudentsExamsCounts($subjectId));
    }
   
    public function getStudentsCountProgressInSubject($subjectId)
    {
        return count($this->subjectRepository->getAllStudentsProgress($subjectId));
    }

    public function getStudentCountSpeedSolvingExamsInSubject($subjectId)
    {
        return count($this->examRepository->getStudentsSpeedPercentageOrderInSubject($subjectId));
    }
}
