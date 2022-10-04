<?php


namespace App\OurEdu\Exams\UseCases\HandelExamQuestionTimeUseCase;

use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Models\ExamQuestionTime;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class HandelExamQuestionTimeUseCase implements HandelExamQuestionTimeUseCaseInterface
{
    private $repository;

    public function __construct(ExamRepositoryInterface $examRepository)
    {
        $this->repository = $examRepository;
    }

    public function handleTime(LengthAwarePaginator $questions, $currentQuestionId)
    {
        $this->repository->endLastExamQuestionTime($currentQuestionId);

        $examQuestion = $questions->first();
        $this->insertStart($examQuestion);
    }

    public function insertStart(ExamQuestion $examQuestion)
    {
        $this->repository->createExamTime($examQuestion);
    }

    public function endAllOpenQuestions($examId)
    {
        $exam = $this->repository->findOrFail($examId);
        $examRepository = new ExamRepository($exam);
        $examRepository->endAllOpenQuestions();

        $this->calculateTime($examRepository, $examId);
    }

    public function calculateTime($examRepository, $examId)
    {
        $times = ExamQuestionTime::
        select(DB::raw('TIME_TO_SEC(TIMEDIFF(`end`, `start`)) as sub_diff_time'), 'exam_question_id', 'end', 'start')
            ->groupBy('exam_question_id', 'end', 'start')
            ->where('exam_id', '=', $examId)
            ->get();
        $times = $times->groupBy('exam_question_id');
        $times->each(function (&$item) {
            $item->sub_diff_time_avg = $item->avg('sub_diff_time');
        });

        foreach ($times as $key => $value) {
            $examRepository->updateExamQuestion($key, ['student_time_to_solve' => $value->sub_diff_time_avg]);
        }
    }

    public function endLastExamQuestionTime($currentQuestionId)
    {
        if (ExamQuestion::find($currentQuestionId)->examQuestionTimes()->exists()) {
            $examQuestionTime = ExamQuestion::find($currentQuestionId)->examQuestionTimes()->latest();
            $examQuestionTime->update([
                'end' => now()
            ]);
        }
    }
}
