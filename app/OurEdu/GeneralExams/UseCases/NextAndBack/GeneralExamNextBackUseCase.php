<?php

namespace App\OurEdu\GeneralExams\UseCases\NextAndBack;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\GeneralExams\Repository\GeneralExam\GeneralExamRepository;
use App\OurEdu\GeneralExams\Repository\GeneralExam\GeneralExamRepositoryInterface;
use App\OurEdu\GeneralExams\Repository\GeneralExamStudent\GeneralExamStudentRepositoryInterface;

/**
 * Next and previous question  use case
 */
class GeneralExamNextBackUseCase implements GeneralExamNextBackUseCaseInterface
{
    protected $user;
    protected $examRepository;
    protected $generalExamStudentRepository;

    public function __construct(GeneralExamRepositoryInterface $examRepository, GeneralExamStudentRepositoryInterface $generalExamStudentRepository)
    {
        $this->user = Auth::guard('api')->user();
        $this->examRepository = $examRepository;
        $this->generalExamStudentRepository = $generalExamStudentRepository;
    }

    public function nextOrBackQuestion(int $examId, int $page)
    {
        $exam = $this->examRepository->findOrFail($examId);

        $studentExam = $this->generalExamStudentRepository->findStudentExam($examId, $this->user->student->id);

        if (! $studentExam) {
            throw new ErrorResponseException(trans("api.Exam not started yet"));
        }

        $examRepo = new GeneralExamRepository($exam);

        $questions = $examRepo->returnQuestion($page);

        if ($questions->currentPage() > $questions->lastPage()) {
            throw new ErrorResponseException(trans("exam.This question not found"));
        }

        return $questions;
    }
}
