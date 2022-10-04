<?php


namespace App\OurEdu\SchoolAdmin\GeneralQuizzesReports\Exports;


use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class TotalPercentageReportExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    private $branches;

    /**
     * TotalPercentageReportExport constructor.
     * @param $branches
     */
    public function __construct($branches)
    {
        $this->branches = $branches;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];

        foreach ($this->branches as $branch) {
            $rows[] = [
                $branch->name,
                $branch->students_count,
                $branch->general_quizzes_count,
                $branch->general_quizzes_score_average,
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
            trans('general_quizzes.branch name'),
            trans('general_quizzes.Students Count'),
            trans('general_quizzes.Quizzes Count'),
            trans('general_quizzes.Average Score'),
        ];
    }
}
