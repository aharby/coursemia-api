<?php


namespace App\OurEdu\SchoolAccounts\InstructorRates\Supervisor\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
//use Maatwebsite\Excel\Events\BeforeSheet;

class InstructorRatesExport extends BaseExport implements WithMapping, ShouldAutoSize, WithEvents
{

    /**
     * @param mixed $rate
     * @return array
     */
    public function map($rate): array
    {
        return [
            $rate->user->name,
            $rate->comment,
            $rate->rating,
            $rate->created_at,
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
        }];
    }
}
