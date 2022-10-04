<?php


namespace App\OurEdu\GeneralQuizzesReports\SchoolSupervisor\Exports;


use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class InstructorLevelReportExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    private $generalQuizzes;

    /**
     * InstructorLevelReportExport constructor.
     * @param $generalQuizzes
     */
    public function __construct($generalQuizzes)
    {
        $this->generalQuizzes = $generalQuizzes;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];
        foreach ($this->generalQuizzes as $generalQuizz) {
            $rows[] = [
                $generalQuizz->branch->name ?? '',
                $generalQuizz->creator->name ?? '',
                $generalQuizz->gradeClass->title ?? '',
                $generalQuizz->subject->name ?? '',
                trans('quiz.' . $generalQuizz->quiz_type) ?? '',
                $generalQuizz->title,
                \Carbon\Carbon::parse($generalQuizz->start_at)->format('Y/m/d') ?? '',
                \Carbon\Carbon::parse($generalQuizz->start_at)->format('h:i a') ?? '',
                \Carbon\Carbon::parse($generalQuizz->end_at)->format('h:i a') ?? '',
                $generalQuizz->studentsAnswered->count() ?? 0,
                $generalQuizz->highest_grade ?? 0.0,
                $generalQuizz->lower_grade ?? 0.0,
                $generalQuizz->successful_percentage ?? 0.0,
            ];
        }

        return $rows;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle("A1:Z1")->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            }];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('quiz.Branch Name'),
            trans('quiz.instructor'),
            trans('quiz.gradeClass'),
            trans('quiz.subject'),
            trans('quiz.type'),
            trans('quiz.name'),
            trans('quiz.time'),
            trans('quiz.started_at'),
            trans('quiz.ended_at'),
            trans('general_quizzes.Attend Students'),
            trans('general_quizzes.highest grade'),
            trans('general_quizzes.lower grade'),
            trans('general_quizzes.Average Score'),
        ];
    }
}
