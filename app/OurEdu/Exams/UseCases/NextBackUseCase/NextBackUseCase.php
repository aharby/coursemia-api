<?php


namespace App\OurEdu\Exams\UseCases\NextBackUseCase;

use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;

class NextBackUseCase implements NextBackUseCaseInterface
{
    private $examRepository;
    private $question;

    public function __construct(
        ExamRepositoryInterface $ExamRepository,
        ExamQuestion $examQuestion
    ) {
        $this->examRepository = $ExamRepository;
        $this->question = $examQuestion;
    }

    public function nextOrBackQuestion(int $examId, int $page)
    {
        $exam = $this->examRepository->findOrFail($examId);

        if ($exam->is_started == 1) {
            $exam = $this->examRepository->findOrFail($examId);
            $examRepo = new ExamRepository($exam);
            $nextOrBackQuestion = $examRepo->returnQuestion($page);
            if ($nextOrBackQuestion->currentPage() > $nextOrBackQuestion->lastPage()) {
                $returnArr['status'] = 404;
                $returnArr['detail'] = trans('exam.This question not found');
                $returnArr['title'] = 'This question not found';
                return $returnArr;
            } else {
                if ($nextOrBackQuestion->currentPage() == $nextOrBackQuestion->lastPage()) {
                    $returnArr['last_question'] = true;
                }
                $returnArr['status'] = 200;
                $returnArr['questions'] = $nextOrBackQuestion;
                return $returnArr;
            }
        } else {
            $returnArr['status'] = 422;
            $returnArr['detail'] = trans(
                'exam.This exam not started yet',
                ['type' => trans('exam.' . $exam->type)]
            );
            $returnArr['title'] = 'This exam didnt start yet';
            return $returnArr;
        }
    }
}
