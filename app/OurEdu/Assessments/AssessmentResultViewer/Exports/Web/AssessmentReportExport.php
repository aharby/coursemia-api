<?php


namespace App\OurEdu\Assessments\AssessmentResultViewer\Exports\Web;


use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;

class AssessmentReportExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    private $assessments;

    /**
     * AssessmentReportExport constructor.
     * @param $assessments
     */
    public function __construct($assessments)
    {
        $this->assessments = $assessments;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];

        foreach ($this->assessments as $assessment) {
            $rows[] = [
                $assessment->title ?? '',
                date('d-m-Y', strtotime($assessment->start_at)),
                date('H:i', strtotime($assessment->start_at)),
                date('d-m-Y', strtotime($assessment->end_at)),
                date('H:i', strtotime($assessment->end_at)),
                $assessment->assessor_type ? trans('app.' . $assessment->assessor_type) : '',
                $assessment->assessee_type ? trans('app.' . $assessment->assessee_type) : '',
                $assessment->average_score . '/' . $assessment->mark,
                (string) $assessment->pivot->assessed_assesses_count ?? "0",
                (string)$assessment->pivot->total_assesses_count ?? "0"
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
            trans('assessment.name'),
            trans('assessment.starting date'),
            trans('assessment.starting time'),
            trans('assessment.finishing date'),
            trans('assessment.finishing time'),
            trans('assessment.assessor_type'),
            trans('assessment.assessee_type'),
            trans('assessment.avg_score'),
            trans('assessment.assessed_assesses_count'),
            trans('assessment.total_assesses_count'),
        ];
    }
}
