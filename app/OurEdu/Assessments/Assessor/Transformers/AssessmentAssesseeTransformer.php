<?php


namespace App\OurEdu\Assessments\Assessor\Transformers;


use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\User;
use League\Fractal\TransformerAbstract;

class AssessmentAssesseeTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
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

    public function transform(User $user)
    {
        return [
            "id" => (int)$user->id,
            "name" => (string) $user->name,
            "assessment_title" => $this->assessment->title
        ];
    }

    public function includeActions(User $user)
    {
        $actions = [];

        if ($this->assessment) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('assessments.assessor.post.startAssessment',
                    [
                        'assessmentId' => $this->assessment->id,
                        'assesseeId' => $user->id
                    ]),
                'label' => trans('assessment.start assessment'),
                'method' => 'POST',
                'key' => APIActionsEnums::START_ASSESSMENT
            ];
        }

        if(count($actions) > 0 ) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
