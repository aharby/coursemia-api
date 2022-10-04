<?php


namespace App\OurEdu\GeneralQuizzes\SchoolSupervisor\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class TrashedGeneralQuizzesExport extends BaseExport implements WithMapping, ShouldAutoSize
{

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($generalQuiz): array
    {
        return [
            'id' => (int)$generalQuiz->id,
            'type' => (string)trans("general_quizzes.".$generalQuiz->quiz_type),
            'title' => (string)$generalQuiz->title,
            'instructor' =>$generalQuiz->creator->name,
            'start_at' => (string)$generalQuiz->start_at,
            'end_at' => (string)$generalQuiz->end_at,
            'subject_id' => $generalQuiz->subject->name ?? " ",
            'avg'=> $generalQuiz->getHomeworkAvgAttribute(),
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('reports.id'),
            trans('quiz.type'),
            trans('quiz.name'),
            trans('quiz.instructor'),
            trans('quiz.started_at'),
            trans('quiz.ended_at'),
            trans('quiz.subject'),
            trans('quiz.grade_average'),
        ];
    }

}
