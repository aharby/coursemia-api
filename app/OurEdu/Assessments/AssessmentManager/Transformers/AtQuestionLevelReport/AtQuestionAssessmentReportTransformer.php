<?php


namespace App\OurEdu\Assessments\AssessmentManager\Transformers\AtQuestionLevelReport;


use App\OurEdu\Assessments\AssessmentManager\Transformers\ResultViewTypesTransformer;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class AtQuestionAssessmentReportTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
        'assessmentResultViewerTypes'
    ];

    protected array $availableIncludes = [
    ];
    /**
     * @var array
     */
    private $params;

    /**
     * AtQuestionAssessmentReportTransformer constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function transform(Assessment $assessment)
    {

        $assessmentTotalMark = $assessment->average_total_mark > 0 ? $assessment->average_total_mark : $assessment->mark;
        $scorePercentage = $assessmentTotalMark > 0 ? ($assessment->average_score / $assessmentTotalMark) * 100 : 0;

        return [
            'id' => (int)$assessment->id,
            'title' => (string)$assessment->title,
            'introduction' => (string)$assessment->introduction,
            'start_at' => (string)Carbon::parse($assessment->start_at)->format('Y-m-d'),
            'end_at' => (string)Carbon::parse($assessment->end_at)->format('Y-m-d'),
            'start_time'=>(string)Carbon::parse($assessment->start_at)->format('H:i'),
            'end_time'=>(string)Carbon::parse($assessment->end_at)->format('H:i'),
            'assessor_type' => (string)$assessment->assessor_type,
            'assessee_type' => (string)$assessment->assessee_type,
            'score_percentage' => number_format($scorePercentage, 2, ".", ""),
            'mark'=>(float)$assessment->mark + $assessment->skipped_questions_grades,
        ];
    }

    public function includeActions(Assessment $assessment)
    {
        $actions = [];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.assessments.assessment-manager.questions-report', ['assessment' => $assessment->id]),
            'label' => trans('assessment.question report'),
            'method' => 'GET',
            'key' => APIActionsEnums::ASSESSOR_REPORT
        ];


        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    private function getAssessmentScore(Assessment $assessment): float
    {
        if ($this->params["hasBranch"]) {
            return $assessment->assessmentBranchesScores[0]->pivot->score ?? 0.0;
        }

        return $assessment->average_score;
    }

    public function includeAssessmentResultViewerTypes(Assessment $assessment)
    {
        return $this->collection(
            $assessment->resultViewerTypes,
            new ResultViewTypesTransformer(),
            ResourceTypesEnums::ASSESSMENT_VIEWER_TYPE
        );
    }

}
