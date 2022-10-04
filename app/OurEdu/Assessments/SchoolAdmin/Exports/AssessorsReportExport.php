<?php


namespace App\OurEdu\Assessments\SchoolAdmin\Exports;

use App\OurEdu\Assessments\Models\Assessment;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class AssessorsReportExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    private $assessors;
    /**
     * @var Assessment
     */
    private $assessment;

    /**
     * AssessorsReportExport constructor.
     * @param Assessment $assessment
     * @param $assessors
     */
    public function __construct(Assessment $assessment, $assessors)
    {
        $this->assessors = $assessors;
        $this->assessment = $assessment;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];

        foreach ($this->assessors as $assessor) {
            $rows[] = [
                $assessor->user->first_name . ' ' . $assessor->user->last_name,
                $assessor->user->branch ? $assessor->user->branch->name : '',
                number_format(($assessor->average_score / $assessor->average_total_mark) * 100, 2),
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
            trans('assessment.assessor_name'),
            trans('assessment.branch'),
            trans('assessment.avg_score'),
        ];
    }
}
