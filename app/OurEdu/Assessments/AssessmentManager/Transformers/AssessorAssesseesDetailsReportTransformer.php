<?php

namespace App\OurEdu\Assessments\AssessmentManager\Transformers;

use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use League\Fractal\TransformerAbstract;

class AssessorAssesseesDetailsReportTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(AssessmentUser $assessmentUser)
    {
        $scorePercentage = $assessmentUser->total_mark > 0 ? ($assessmentUser->score/$assessmentUser->total_mark)*100:0;

        $transformedData = [
            'id' => (int)$assessmentUser->assessee_id,
            'attempt_id' => (int)$assessmentUser->id,
            'name' => (string)$assessmentUser->assessee->name,
            'score_percentage' => (float)number_format($scorePercentage, 2),
            'general_comment' => (string)$assessmentUser->general_comment,
            'is_finished' => (bool)$assessmentUser->is_finished ? true : false,
            'start_at' => (string)Carbon::parse($assessmentUser->start_at)->format('Y-m-d H:i'),
            'end_at' => (string)Carbon::parse($assessmentUser->end_at)->format('Y-m-d H:i'),
            'assessment_mark' => (float)$assessmentUser->assessment->mark + $assessmentUser->assessment->skipped_questions_grades,
            'assessment_rate' => $this->getAssessmentRate($assessmentUser->assessment->rates, $assessmentUser->score ?? 0.0)
        ];

        return $transformedData;
    }

    public function includeActions(AssessmentUser $assessmentUser)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.assessments.result-viewers.assessor-assessee-answer',
                [
                    'assessmentUser' => $assessmentUser->id
                ]
            ),
            'label' => trans('assessment.answers'),
            'method' => 'GET',
            'key' => APIActionsEnums::ASSESSMENT_ANSWERS
        ];

        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    private function getAssessmentRate(Collection $assessmentPointsRate, float $score)
    {
        foreach ($assessmentPointsRate->toArray() as $Key => $assessment) {
            if ($score <= $assessment['max_points'] && $score >= $assessment['min_points']) {
                return $assessment['rate'];
            }
        }
    }
}
