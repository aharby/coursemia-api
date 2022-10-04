<?php

namespace App\OurEdu\Assessments\AssessmentManager\Transformers;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use Carbon\Carbon;

class AssessmentReportTransformer extends TransformerAbstract
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

    public function transform(Assessment $assessment)
    {
        $user= $this->params['user'] ?? auth()->user();
        $assessed_assesses_count =  $assessment->assessed_assesses_count;
        $total_assesses_count =  $assessment->total_assesses_count;

        if($user->type !== UserEnums::ASSESSMENT_MANAGER  ){
            $viewerPivot = $assessment->authResultViewer->first()->pivot;

            $assessed_assesses_count =  $viewerPivot->assessed_assesses_count;
            $total_assesses_count =  $assessment->total_assesses_count;
        }

        $assessmentTotalMark = $assessment->average_total_mark > 0 ? $assessment->average_total_mark : $assessment->mark;
        $scorePercentage = $assessmentTotalMark > 0 ? ($assessment->average_score / $assessmentTotalMark) * 100 : 0;

        $transformedData = [
            'id' => (int)$assessment->id,
            'title' => (string)$assessment->title,
            'introduction' => (string)$assessment->introduction,
            'start_at' => (string)Carbon::parse($assessment->start_at)->format('Y-m-d'),
            'end_at' => (string)Carbon::parse($assessment->end_at)->format('Y-m-d'),
            'start_time'=>(string)Carbon::parse($assessment->start_at)->format('H:i'),
            'end_time'=>(string)Carbon::parse($assessment->end_at)->format('H:i'),
            'assessor_type' => (string)$assessment->assessor_type,
            'assessee_type' => (string)$assessment->assessee_type,
            'assessment_viewer_type' => (string)$assessment->assessment_viewer_type,
            'score_percentage' => number_format($scorePercentage, 2, ".", ""),
            'mark'=>(float)$assessment->mark + $assessment->skipped_questions_grades,
            'assessed_assesses_count' => (string) $assessed_assesses_count ,
            'total_assesses_count' =>    (string)  $total_assesses_count
        ];

        return $transformedData;
    }

    public function includeActions(Assessment $assessment)
    {
        $actions = [];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.assessments.assessment-manager.assessors-report', ['assessment' => $assessment->id]),
            'label' => trans('assessment.assessor_report'),
            'method' => 'GET',
            'key' => APIActionsEnums::ASSESSOR_REPORT
        ];


        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
