<?php


namespace App\OurEdu\Assessments\SchoolAdmin\Exports;


use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Users\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class AssessorAssessesReportExport implements FromArray, WithEvents, WithHeadings, ShouldAutoSize
{
    /**
     * @var Assessment
     */
    private $assessment;
    private $assesses;

    /**
     * AssessorAssessesReportExport constructor.
     * @param Assessment $assessment
     * @param $assesses
     */
    public function __construct(Assessment $assessment, $assesses)
    {
        $this->assessment = $assessment;
        $this->assesses = $assesses;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];

        foreach ($this->assesses as $assess) {
            $rows[] = [
                $assess->assessee->first_name . ' ' . $assess->assessee->last_name,
                number_format(($assess->score/$assess->total_mark)*100, 2),
                $this->getAssessmentRate($this->assessment, $assess),
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
            trans('assessment.assessee_name'),
            trans('assessment.score'),
            trans('assessment.rate'),
        ];
    }

    private function getAssessmentRate(Assessment $assessment, $assess)
    {
        return $assessment->rates
            ? $assessment->rates->where('min_points', '<=', $assess->score)
                ->where('max_points', '>=', $assess->score)
                ->first()->rate ?? ""
            : '';
    }
}
