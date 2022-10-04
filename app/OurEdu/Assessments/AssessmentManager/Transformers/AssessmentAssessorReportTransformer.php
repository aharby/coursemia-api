<?php

namespace App\OurEdu\Assessments\AssessmentManager\Transformers;

use App\OurEdu\Assessments\Models\AssessmentAssessor;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Users\UserEnums;

class AssessmentAssessorReportTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
    ];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(AssessmentAssessor $assessor)
    {
        $userType = $assessor->user->type;
        $scorePercentage = $assessor->average_total_mark ? ($assessor->average_score/$assessor->average_total_mark)*100 : 0;
        $authUser = auth()->user();

        if ($authUser->type !== UserEnums::ASSESSMENT_MANAGER) {
            $assessorViewer = $authUser->assessorViewerAvgScores()
                ->where([
                    ['assessor_id', '=', $assessor->user_id],
                    ['assessment_id', '=', $assessor->assessment_id],
                ])
                ->first();

            if ($assessorViewer) {
                $scorePercentage = $assessorViewer->average_total_mark > 0 ? ($assessorViewer->average_score / $assessorViewer->average_total_mark) * 100: 0;
            }
        }

        $transformedData = [
            'id' => (int)$assessor->user_id,
            'name'=>(string)$assessor->user->name,
            'user_id' => (int) $assessor->user->id,
            'score_percentage'=>(float)number_format($scorePercentage, "2", ".", ""),
            'assessment_mark'=>(float)$assessor->assessment->mark + $assessor->assessment->skipped_questions_grades,
            'assessment_title'=>(string)$assessor->assessment->title ?? ''
        ];

        if ($userType == UserEnums::SCHOOL_ACCOUNT_MANAGER) {
            $branches = $assessor->assessment->schoolAccount->branches()->pluck("name")->toArray();
            $transformedData['branch'] = implode(', ', $branches);
        } else if($userType == UserEnums::EDUCATIONAL_SUPERVISOR && $assessor->user->branches()->count()>0){
            $transformedData['branch'] = implode(', ' ,$assessor->user->branches->pluck('name')->toArray());
        }else{
            $transformedData['branch'] = $assessor->user->schoolAccountBranchType->schoolAccount->name.': '.$assessor->user->schoolAccountBranchType->name;
        }


        return $transformedData;
    }

    public function includeActions(AssessmentAssessor $assessor)
    {
        $actions = [];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.assessments.assessment-manager.assessor-assessee-report', [
                'assessment' => $assessor->assessment_id,'assessorId'=>$assessor->user_id
            ]),
            'label' => trans('assessment.assessment_assessee_report'),
            'method' => 'GET',
            'key' => APIActionsEnums::ASSESSEE_REPORT
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
