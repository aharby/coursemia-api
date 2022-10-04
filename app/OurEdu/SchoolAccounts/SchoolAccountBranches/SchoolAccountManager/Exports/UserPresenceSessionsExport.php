<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class UserPresenceSessionsExport extends BaseExport implements WithMapping, WithEvents
{
    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        if (!$row->vcrSession) {
            return [];
        }

        return [
            optional($row->vcrSession->classroom)->name,
            optional($row->vcrSession->classroom->branch)->name,
            optional($row->vcrSession->subject)->name,
            optional($row->vcrSession->instructor)->first_name,
            optional($row->vcrSession->classroomClassSession)->from_time,
            optional($row->vcrSession->classroomClassSession)->to_time,
            optional($row->vcrSession->classroomClassSession)->from_date,
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
            }];    }
}
