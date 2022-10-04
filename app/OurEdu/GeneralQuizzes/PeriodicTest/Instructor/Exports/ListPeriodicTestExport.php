<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class ListPeriodicTestExport extends BaseExport implements WithMapping, ShouldAutoSize
{

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($periodicTest): array
    {
        return [
            'id' => (int)$periodicTest->id,
            'title' => (string)$periodicTest->title,
            'random_question' => (string)($periodicTest->random_question ? trans("app.Yes") : trans("app.No")),
            'quiz_type' => (string)trans("general_quizzes.".$periodicTest->quiz_type),
            'start_at' => (string)$periodicTest->start_at,
            'end_at' => (string)$periodicTest->end_at,
            'is_published' => (bool)!is_null($periodicTest->published_at) ? trans("app.Publish") : trans("app.Not Published"),
            'published_at' => (string)$periodicTest->published_at,
            'subject_id' => $periodicTest->subject->name ?? " ",
            'grade_class_id' => $periodicTest->gradeClass->title ?? "",
            'avg'=> $periodicTest->getHomeworkAvgAttribute(),
            'mark'=> (float)$periodicTest->mark,
            'is_repeated' => $periodicTest->is_repeated ? trans("app.Yes") : trans("app.No"),
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('reports.id'),
            trans('quiz.name'),
            trans('general_quizzes.random question'),
            trans('quiz.type'),
            trans('quiz.started_at'),
            trans('quiz.ended_at'),
            trans('quiz.publishing Status'),
            trans('quiz.published at'),
            trans('quiz.subject'),
            trans('classrooms.grade classes'),
            trans('quiz.grade_average'),
            trans('general_quizzes.score'),
            trans('general_quizzes.is_retaken'),
        ];
    }

}
