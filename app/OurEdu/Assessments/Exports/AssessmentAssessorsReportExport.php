<?php


namespace App\OurEdu\Assessments\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use App\OurEdu\Users\UserEnums;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssessmentAssessorsReportExport extends BaseExport implements WithMapping, ShouldAutoSize
{

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($assessor): array
    {
        if ($assessor->user->type == UserEnums::SCHOOL_ACCOUNT_MANAGER) {
            $branches = $assessor->assessment->schoolAccount->branches()->pluck("name")->toArray();
            $branch = implode(', ', $branches);
        } else if($assessor->user->type == UserEnums::EDUCATIONAL_SUPERVISOR && $assessor->user->branches()->count()>0){
            $branch = implode(', ' ,$assessor->user->branches->pluck('name')->toArray());
        }else{
            $branch = $assessor->user->schoolAccountBranchType->schoolAccount->name.': '.$assessor->user->schoolAccountBranchType->name;
        }
        $scorePercentage = $assessor->average_total_mark ? ($assessor->average_score/$assessor->average_total_mark)*100 : 0;

        return [
            'assessor_name'=>(string)$assessor->user->name,
            'branch'=>$branch,
            'average_score'=>(string)number_format($scorePercentage, "2", ".", ""),
            // 'assessment_mark'=>,
            // 'assessment_title'=>(string)$assessor->assessment->title,
            // 'assessment_average_score'=>(float)$assessor->assessment->average_score,
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
            // trans('assessment.assessment_mark'),
            // trans('assessment.assessment_title'),
            // trans('assessment.assessment_average_score'),
        ];
    }

}
