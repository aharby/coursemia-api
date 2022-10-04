<?php


namespace App\OurEdu\VCRSessions\Admin\Exports;


use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class SubjectsVCRSessionsExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    private $subjects;

    /**
     * SubjectVCRSessionsExport constructor.
     * @param $subjects
     */
    public function __construct($subjects)
    {
        $this->subjects = $subjects;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];

        foreach ($this->subjects as $subject) {
            $daysCount = 0;
            foreach ($subject->VCRSchedules as $schedule)
                foreach ($schedule->workingDays as $day)
                    $daysCount += count(dayRepeated($day->day, $schedule->from_date, $schedule->to_date));

            $rows[] = [
                $subject->name,
                $subject->gradeClass->title,
                $subject->v_c_r_sessions_count + $daysCount,
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
            }
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('VCRSessions.Subject Name'),
            trans('VCRSessions.grade class'),
            trans('VCRSessions.sessions count'),
        ];
    }
}
