<?php


namespace App\OurEdu\VCRSessions\Admin\Exports;


use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class SubjectVCRSessionsPresenceExport implements FromArray,WithHeadings, ShouldAutoSize, WithEvents
{
    private $VCRSessions;

    /**
     * SubjectVCRSessionsPresenceExport constructor.
     * @param $VCRSessions
     */
    public function __construct($VCRSessions)
    {
        $this->VCRSessions = $VCRSessions;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];

        foreach($this->VCRSessions as $VCRSession) {
            $rows[] = [
                $VCRSession->time_to_start ? date("d-m-Y", strtotime($VCRSession->started_at)) : "",
                $VCRSession->instructor->name,
                $VCRSession->time_to_start ? date("H:i:s", strtotime($VCRSession->time_to_start)) : "",
                $VCRSession->time_to_end ? date("H:i:s", strtotime($VCRSession->time_to_end)) : "",
                $VCRSession->v_c_r_session_presence_count ?? "0",
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
            trans('VCRSessions.date'),
            trans('VCRSessions.Instructor Name'),
            trans('VCRSessions.started at'),
            trans('VCRSessions.ended at'),
            trans('VCRSessions.attendance number'),
        ];
    }
}
