<?php


namespace App\OurEdu\GeneralQuizzesReports\SchoolManager\Exports;

use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class BranchLevelReportExport extends BaseExport implements WithMapping, ShouldAutoSize, WithEvents
{

    /**
     * Mapping Row
     * @param $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->branch->name,
            $row->subject->name,
            trans('quiz.' . $row->quiz_type) ?? '',
            $row->title,
            \Carbon\Carbon::parse($row->start_at)->format("Y/m/d") ?? '',
            \Carbon\Carbon::parse($row->start_at)->format("h:i a") ?? '',
            \Carbon\Carbon::parse($row->end_at)->format("h:i a") ?? '',
            round($row->studentsAnswered->average('score_percentage'), 2) ?? 0,
        ];
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
            }
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('quiz.Branch Name'),
            trans('quiz.subject'),
            trans('quiz.type'),
            trans('quiz.name'),
            trans('quiz.date'),
            trans('quiz.started_at'),
            trans('quiz.ended_at'),
            trans('general_quizzes.Average Score'),
        ];
    }
}
