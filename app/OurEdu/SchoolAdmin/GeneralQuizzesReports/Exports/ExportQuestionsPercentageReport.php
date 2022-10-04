<?php

namespace App\OurEdu\SchoolAdmin\GeneralQuizzesReports\Exports;

use App\OurEdu\BaseApp\Exports\BaseExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportQuestionsPercentageReport extends BaseExport implements WithMapping, ShouldAutoSize
{
    public function map($quiz): array
    {
        return [
            'branch_name' => $quiz->branch->name ?? '',
            'gradeClass' => $quiz->gradeClass->title ?? '',
            'subject' => $quiz->subject->name ?? '',
            'type' => trans('quiz.'.$quiz->quiz_type) ?? '',
            'name' => $quiz->title,
            'start_date' => Carbon::parse($quiz->start_at)->format('Y/m/d') ?? '',
            'end_date' => Carbon::parse($quiz->end_at)->format('Y/m/d') ?? '',
            'started_at' => Carbon::parse($quiz->start_at)->format('h:i a') ?? '',
            'ended_at' => Carbon::parse($quiz->end_at)->format('h:i a') ?? '',
           'Attend_Students' => $quiz->attend_students ?? 0,
           'highest_grade' => $quiz->highest_grade ?? 0.00,
           'lower_grade' => $quiz->lower_grade ?? 0.00,
           'Average_Score' => round($quiz->studentsAnswered->average('score_percentage'), 2).'%',
            ];
    }

    public function headings(): array
    {
        return [
        trans('quiz.Branch Name'),
        trans('quiz.gradeClass'),
        trans('quiz.subject'),
        trans('quiz.type'),
        trans('quiz.name'),
        trans('quiz.start_date'),
        trans('quiz.end_date'),
        trans('quiz.started_at'),
        trans('quiz.ended_at'),
        trans('general_quizzes.Attend Students'),
        trans('general_quizzes.highest grade'),
        trans('general_quizzes.lower grade'),
        trans('general_quizzes.Average Score'),
        ];
    }
}
