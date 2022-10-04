<?php


namespace App\OurEdu\SchoolAccounts\Reports\Exports;


use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class TeacherSessionsAttendanceExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
     * @var array
     */
    private $instructors;

    /**
     * TeacherSessionsAttendanceExport constructor.
     * @param array $instructors
     */
    public function __construct($instructors)
    {
        $this->instructors = $instructors;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];
        foreach ($this->instructors as $instructor) {
            $rows[] = [
                $instructor->name ??'',
                $instructor->username ?? '',
                $instructor->branch->name ??'',
                $instructor->school_instructor_sessions_count,
                $instructor->v_c_r_sessions_presence_count,
                ($instructor->school_instructor_sessions_count ?? 0) - ($instructor->v_c_r_sessions_presence_count ?? 0),
            ];
        }

        return $rows;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('instructors.Instructor Name'),
            trans('instructors.Instructor ID'),
            trans('instructors.Branch Name'),
            trans('instructors.Total of Sessions'),
            trans('instructors.Session Attend'),
            trans('instructors.Session Absence'),
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
