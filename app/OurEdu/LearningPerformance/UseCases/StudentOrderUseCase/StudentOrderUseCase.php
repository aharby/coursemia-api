<?php

namespace App\OurEdu\LearningPerformance\UseCases\StudentOrderUseCase;

use App\OurEdu\GeneralExams\Repository\GeneralExamStudent\GeneralExamStudentRepositoryInterface;

class StudentOrderUseCase implements StudentOrderUseCaseInterface
{
    private $GeneralExamStudentRepository;

    public function __construct(
        GeneralExamStudentRepositoryInterface $GeneralExamStudentRepository
    )
    {
        $this->GeneralExamStudentRepository = $GeneralExamStudentRepository;
    }

    public function getStudentOrderInSubject($subjectId ) {
        return $this->GeneralExamStudentRepository->getStudentsOrder($subjectId);
    }

    
    public function getStudentGeneralExamsCount($subjectId)
    {
        return count($this->GeneralExamStudentRepository->getStudentsOrder($subjectId));
    }
}
