<?php


namespace App\OurEdu\SchoolAccounts\InstructorRates\Supervisor\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class AverageInstructorsRatesExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
     * @var array
     */
    private $instructors;

    /**
     * AverageInstructorsRatesExport constructor.
     * @param array $instructors // instructors is array of Users objects
     */
    public function __construct(array $instructors)
    {
        $this->instructors = $instructors;
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
    public function array(): array
    {
        $rows = [];
        foreach ($this->instructors as $instructor) {

            $subjects = "";
            foreach ($instructor->schoolInstructorSubjects as $subject) {
                if (strlen($subjects) > 0 ) {
                    $subjects .= ", ";
                }

                $subjects .= "(" . $subject->name;

                if ($subject->gradeClass) {
                    $subjects .= " - " . $subject->gradeClass->title ?? " ";
                }
                $subjects .= ")";
            }


            $rows[] = [
                $instructor->name,
                $subjects,
                round($instructor->ratings->avg('rating'),1)
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
            trans('instructors.Subject'),
            trans('instructors.Rate'),
        ];
    }
}
