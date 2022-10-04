<?php


namespace App\OurEdu\Assessments\Assessee\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;

class AssessmentTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'actions'
    ];


    /**
     * AssessmentTransformer constructor.
     */
    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }



    public function transform(AssessmentUser $assessmentUser)
    {
        $scorePercentage = $assessmentUser->avg_total_mark > 0 ? ($assessmentUser->avg_score/$assessmentUser->avg_total_mark)*100 : 0;

        return [
            'id' => $assessmentUser->assessment->id,
            'title' => (string)$assessmentUser->assessment->title ?? '',
            'introduction' => (string)$assessmentUser->assessment->introduction,
            'start_at' => (string)Carbon::parse($assessmentUser->assessment->start_at)->format('Y-m-d'),
            'end_at' => (string)Carbon::parse($assessmentUser->assessment->end_at)->format('Y-m-d'),
            'assessor_type' => (string)$assessmentUser->assessment->assessor_type,
            'mark' => (float)$assessmentUser->assessment->mark,
            'score_percentage' => number_format($scorePercentage, 2, ".", ""),
        ];
    }

    public function includeActions(AssessmentUser $assessmentUser)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('assessments.assessee.assessors.list', ['assessment' => $assessmentUser->assessment->id]),
            'label' => trans('app.Details'),
            'method' => 'GET',
            'key' => APIActionsEnums::LIST_ASSESSOR
        ];

        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
