<?php


namespace App\OurEdu\GeneralQuizzes\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class ListGeneralQuizzesExport extends BaseExport implements WithMapping, ShouldAutoSize
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
            'title' => (string)$generalQuiz->title,
            'random_question' => (string)($generalQuiz->random_question ? trans("app.Yes") : trans("app.No")),
            'quiz_type' => (string)trans("general_quizzes.".$generalQuiz->quiz_type),
            'start_at' => (string)$generalQuiz->start_at,
            'end_at' => (string)$generalQuiz->end_at,
            'is_published' => (bool)!is_null($generalQuiz->published_at) ? trans("app.Publish") : trans("app.Not Published"),
            'published_at' => (string)$generalQuiz->published_at,
            'subject_id' => $generalQuiz->subject->name ?? " ",
            'grade_class_id' => $generalQuiz->gradeClass->title ?? "",
            'avg'=> $generalQuiz->getHomeworkAvgAttribute(),
            'mark'=> (float)$generalQuiz->mark,
            'is_repeated' => $generalQuiz->is_repeated ? trans("app.Yes") : trans("app.No"),
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
