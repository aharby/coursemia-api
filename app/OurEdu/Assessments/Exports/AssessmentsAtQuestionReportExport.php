<?php


namespace App\OurEdu\Assessments\Exports;


use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\BaseApp\Exports\BaseExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssessmentsAtQuestionReportExport implements FromArray, ShouldAutoSize, WithHeadings
{
    private $assessments;
    private array $params;

    /**
     * AssessmentsAtQuestionReportExport constructor.
     * @param $assessments
     * @param array $params
     */
    public function __construct($assessments, array $params)
    {
        $this->assessments = $assessments;
        $this->params = $params;
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function array(): array
    {
        $rows = [];
        $serialNumber = 1;

        foreach ($this->assessments as $assessment) {
            $assessmentTotalMark = $assessment->average_total_mark > 0 ? $assessment->average_total_mark : $assessment->mark;
            $scorePercentage = $assessmentTotalMark > 0 ? ($assessment->average_score / $assessmentTotalMark) * 100 : 0;

            $rows[] = [
                $serialNumber++,
                $assessment->title,
                Carbon::parse($assessment->start_at)->format('Y-m-d'),
                Carbon::parse($assessment->end_at)->format('Y-m-d'),
                Carbon::parse($assessment->start_at)->format('H:i'),
                Carbon::parse($assessment->end_at)->format('H:i'),
                (string)trans('app.'.$assessment->assessor_type),
                (string)trans('app.'.$assessment->assessee_type),
                number_format($scorePercentage, 2),
            ];
        }

        return $rows;
    }

    private function getAssessmentScore(Assessment $assessment): float
    {
        if ($this->params["hasBranch"]) {
            return $assessment->assessmentBranchesScores[0]->pivot->score ?? 0.0;
        }

        return $assessment->average_score;
    }

    public function headings(): array
    {
        return [
            trans("assessment.serial_number"),
            trans('assessment.name'),
            trans('assessment.starting date'),
            trans('assessment.finishing date'),
            trans('assessment.starting time'),
            trans('assessment.finishing time'),
            trans('assessment.assessor_type'),
            trans('assessment.assessee_type'),
            trans('assessment.avg_score'),
        ];
    }

}
