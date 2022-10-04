<?php


namespace App\OurEdu\Assessments\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use App\OurEdu\Users\UserEnums;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
class AssessmentReportExport extends BaseExport implements WithMapping, ShouldAutoSize
{

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($assessment): array
    {
        $user=  auth()->user();
        $assessed_assesses_count =  $assessment->assessed_assesses_count;
        $total_assesses_count =  $assessment->total_assesses_count;

        if($user->type !== UserEnums::ASSESSMENT_MANAGER  ){
            $assessed_assesses_count =  $assessment->authResultViewer->first()->pivot->assessed_assesses_count;
            $total_assesses_count =  $assessment->authResultViewer->first()->pivot->total_assesses_count;

        }

        $assessmentTotalMark = $assessment->average_total_mark > 0 ? $assessment->average_total_mark : $assessment->mark;
        $scorePercentage = $assessmentTotalMark > 0 ? ($assessment->average_score / $assessmentTotalMark) * 100 : 0;


        return [
            'title' => (string)$assessment->title,
            'start_at' => (string)Carbon::parse($assessment->start_at)->format('Y-m-d'),
            'start_time'=>(string)Carbon::parse($assessment->start_at)->format('H:i'),
            'end_at' => (string)Carbon::parse($assessment->end_at)->format('Y-m-d'),
            'end_time'=>(string)Carbon::parse($assessment->end_at)->format('H:i'),
            'assessee_type' => (string) trans('app.'.$assessment->assessee_type),
            'assessor_type' => (string) trans('app.'.$assessment->assessor_type),
            'average_score'=>(string)number_format($scorePercentage, 2, ".", ""),
            'assessed_assesses_count' => (string)   $assessed_assesses_count,
            'total_assesses_count' =>    (string)     $total_assesses_count
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('assessment.title'),
            trans('assessment.start_at'),
            trans('assessment.start_time'),
            trans('assessment.end_at'),
            trans('assessment.end_time'),
            trans('assessment.assessee_type'),
            trans('assessment.assessor_type'),
            trans('assessment.avg_score'),
            trans('assessment.assessed_assesses_count'),
            trans('assessment.total_assesses_count'),
        ];
    }

}
