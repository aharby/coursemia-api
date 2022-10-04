<?php


namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class ListHomeworksExport extends BaseExport implements WithMapping, ShouldAutoSize
{

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($homework): array
    {
        return [
            'id' => (int)$homework->id,
            'title' => (string)$homework->title,
            'random_question' => (string)($homework->random_question ? trans("app.Yes") : trans("app.No")),
            'quiz_type' => (string)trans("general_quizzes.".$homework->quiz_type),
            'start_at' => (string)$homework->start_at,
            'end_at' => (string)$homework->end_at,
            'is_published' => (bool)!is_null($homework->published_at) ? trans("app.Publish") : trans("app.Not Published"),
            'published_at' => (string)$homework->published_at,
            'subject_id' => $homework->subject->name ?? " ",
            'grade_class_id' => $homework->gradeClass->title ?? "",
            'avg'=> $homework->getHomeworkAvgAttribute(),
            'mark'=> (float)$homework->mark,
            'is_repeated' => $homework->is_repeated ? trans("app.Yes") : trans("app.No"),
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
