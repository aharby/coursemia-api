<?php


namespace App\OurEdu\Assessments\Assessee\Transformers;


use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use League\Fractal\TransformerAbstract;

class AssessmentAssessorTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    /**
     * @var Assessment|null
     */
    private $assessment;

    /**
     * AssessmentAssesseeTransformer constructor.
     * @param Assessment|null $assessment
     */
    public function __construct(Assessment $assessment = null)
    {
        $this->assessment = $assessment;
    }

    public function transform(AssessmentUser $assessmentUser)
    {
        $scorePercentage = $assessmentUser->total_mark > 0 ? ($assessmentUser->score/$assessmentUser->total_mark)*100:0;

        return [
            "id" => (int)$assessmentUser->assessor->id,
            "name" => (string) $assessmentUser->assessor->name,
            "type" => (string) $assessmentUser->assessor->type,
            "date" => date('d-m-Y', strtotime($assessmentUser->start_at)),
            "time" => date('H:i:s', strtotime($assessmentUser->start_at)),
            "assessment_mark" => $this->assessment->mark,
            "score_percentage" => number_format($scorePercentage, 2),
            "assessment_title" => $this->assessment->title
        ];
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
}
