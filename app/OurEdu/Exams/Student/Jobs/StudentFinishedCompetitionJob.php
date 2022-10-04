<?php

namespace App\OurEdu\Exams\Student\Jobs;

use App\OurEdu\Exams\Events\CompetitionEvents\StudentFinishedCourseCompetition;
use App\OurEdu\Exams\Models\Competitions\CompetitionQuestionStudent;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\UseCases\FinishExamUseCase\FinishExamUseCase;
use App\OurEdu\Users\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StudentFinishedCompetitionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Exam $exam, public Student $student)
    {
    }

    public function handle(FinishExamUseCase $finishExamUseCase, ExamRepository $examRepository)
    {
        $this->updateStudentResults($examRepository);
        $this->fireSocket($finishExamUseCase);
        $this->finishCompetition($examRepository);

        if ($this->exam->is_finished and $this->exam->course) {
            NotifyInstructorFinishedCompetitionJob::dispatch($this->exam);
        }
    }

    private function updateStudentResults(ExamRepository $examRepository): void
    {
        $results = CompetitionQuestionStudent::query()->where('exam_id', $this->exam->id)
            ->where('student_id', $this->student->id)->sum('is_correct_answer');

        $this->exam->competitionStudents()
            ->where('student_id', $this->student->id)
            ->update(['result' => $results,
                'is_finished' => true
            ]);
        $examRepository->updateStudentsRankInCompetition($this->exam);

    }

    private function fireSocket(FinishExamUseCase $finishExamUseCase): void
    {
        $studentBulkOrderInCompetition = $finishExamUseCase->getStudentBulkOrderInCompetition($this->exam, $this->student);
        $studentOrderInCompetition = $finishExamUseCase->getStudentOrderInCompetition($this->exam, $this->student);
        $allStudents = $finishExamUseCase->getAllStudentsCompetition($this->exam);
        $joinedStudents = $finishExamUseCase->getFinishedStudentsInCompetition($this->exam);

        StudentFinishedCourseCompetition::dispatch(
            $studentBulkOrderInCompetition,
            $studentOrderInCompetition,
            $this->student->user,
            $allStudents,
            $joinedStudents,
            $this->exam
        );
    }

    private function finishCompetition(ExamRepository $examRepository): void
    {
        $isAllFinished = $this->exam->competitionStudents()->where('is_finished',false)->first();

        if (!$isAllFinished) {
            $data = [
                'is_finished' => 1,
            ];

            $examRepository->update($this->exam, $data);
        }
    }
}
