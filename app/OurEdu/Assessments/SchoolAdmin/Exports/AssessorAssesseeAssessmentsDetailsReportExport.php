<?php


namespace App\OurEdu\Assessments\SchoolAdmin\Exports;


use App\OurEdu\Assessments\Models\Assessment;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class AssessorAssesseeAssessmentsDetailsReportExport implements FromArray, WithEvents, WithHeadings, ShouldAutoSize
{
    private $assessmentUsers;
    /**
     * @var Assessment
     */
    private $assessment;

    /**
     * AssessorAssesseeAssessmentsDetailsReportExport constructor.
     * @param $assessmentUsers
     * @param Assessment $assessment
     */
    public function __construct($assessmentUsers, Assessment $assessment)
    {
        $this->assessmentUsers = $assessmentUsers;
        $this->assessment = $assessment;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];
        $rates = $this->assessment->rates;

        foreach ($this->assessmentUsers as $assessmentUser) {
            $rows[] = [
                $assessmentUser->assessee->name,
                date("d/m/Y", strtotime($assessmentUser->start_at)),
                date("h:i a", strtotime($assessmentUser->start_at)),
                number_format(($assessmentUser->score/$assessmentUser->total_mark)*100, 2),
                $rates
                    ? $rates->where('min_points', '<=', $assessmentUser->score)->
                    where('max_points', '>=', $assessmentUser->score)
                        ->first()->rate ?? ""
                    : ''
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
        ];    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('assessment.assessee_name'),
            trans('assessment.the date'),
            trans('assessment.the time'),
            trans('assessment.score'),
            trans('assessment.rate'),
        ];
    }
}
