<?php


namespace App\OurEdu\Assessments\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AssessorAssesseeDetailsReportExport extends BaseExport implements WithMapping, ShouldAutoSize
{

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($assessee): array
    {
        $scorePercentage = $assessee->total_mark > 0 ? ($assessee->score/$assessee->total_mark)*100:0;

        return [
            'name' => (string)$assessee->assessee->name,
            'score' => (float)number_format($scorePercentage, 2),
            date("d/m/Y", strtotime($assessee->start_at)),
            date("h:i a", strtotime($assessee->start_at)),
            'assessment_rate' => $this->getAssessmentRate($assessee->assessment->rates, $assessee->score),
            'general_comment' => (string)$assessee->general_comment,
        ];
    }

    private function getAssessmentRate(Collection $assessmentPointsRate, float $score)
    {
        foreach ($assessmentPointsRate->toArray() as $Key => $assessment) {
            if ($score <= $assessment['max_points'] && $score >= $assessment['min_points']) {
                return $assessment['rate'];
            }
        }
    }
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('assessment.assessee_name'),
            trans('assessment.score'),
            trans('assessment.the date'),
            trans('assessment.the time'),
            trans('assessment.assessment_rate'),
            trans('assessment.general_comment'),

        ];
    }

}
