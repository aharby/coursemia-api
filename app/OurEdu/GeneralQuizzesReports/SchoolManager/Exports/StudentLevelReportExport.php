<?php


namespace App\OurEdu\GeneralQuizzesReports\SchoolManager\Exports;

use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class StudentLevelReportExport extends BaseExport implements WithMapping, ShouldAutoSize, WithEvents
{

    /**
     * Mapping Row
     * @param $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->classroom->branch->name,
            $row->gradeClass->title,
            $row->classroom->name,
            $row->user->username,
            $row->user->name,
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
            trans('app.School Account Branches'),
            trans('students.grade class'),
            trans('students.classroom'),
            trans('students.NO'),
            trans('students.student name'),
        ];
    }
}
