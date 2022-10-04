<?php

namespace App\OurEdu\Assessments\AssessmentManager\Transformers;

use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use League\Fractal\TransformerAbstract;

class AssessorAssesseesReportTransformer extends TransformerAbstract
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

    public function transform(AssessmentUser $assessmentUser)
    {
        $scorePercentage = $assessmentUser->total_mark > 0 ? ($assessmentUser->score/$assessmentUser->total_mark)*100:0;
        $transformedData = [
            'id' => (int)$assessmentUser->assessee_id,
            'name' => (string)$assessmentUser->assessee->name,
            'score_percentage' => (float)number_format($scorePercentage, 2),
            'assessment_mark' => (float)$assessmentUser->assessment->mark+$assessmentUser->assessment->skipped_questions_grades,
        ];

        return $transformedData;
    }

    public function includeActions(AssessmentUser $assessmentUser)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.assessments.assessment-manager.assessor-assessee-details-report', [
                'assessment' => $assessmentUser->assessment_id,
                'assessor'=>$assessmentUser->user_id,
                'assessee'=>$assessmentUser->assessee_id
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
