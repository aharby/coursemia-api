<?php

namespace App\OurEdu\SchoolAccounts\Reports\Exports;

use App\OurEdu\BaseApp\Exports\BaseExport;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class StudentsAttendanceExport extends BaseExport implements WithMapping, ShouldAutoSize, WithEvents
{
    /**
     * @var Collection
     */
    private $classSessions;

    /**
     * StudentsAttendanceExport constructor.
     * @param Collection $collection
     * @param Collection $classSessions
     * @param array $heading
     */
    public function __construct(Collection $collection, Collection $classSessions, array $heading = [])
    {
        $this->classSessions = $classSessions;
        parent::__construct($collection, $heading);
    }

    /**
     * Mapping Row
     * @param $row
     * @return array
     */
    public function map($row): array
    {
        $rowData = [
            $row->first_name . ' ' . $row->last_name,
            $row->username,

        ];

        foreach ($this->classSessions as $session)

            if (in_array($session->id, $row->attendSessions)) {
                $rowData[] = "✔";
            } else {
                $rowData[] = "X";
            }

        return $rowData;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {

                $event->sheet->getStyle('A1:Z1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);

            } ,
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle("A1:Z1")->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            }
        ];
    }
}
