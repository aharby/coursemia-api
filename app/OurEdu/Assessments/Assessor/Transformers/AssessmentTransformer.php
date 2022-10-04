<?php


namespace App\OurEdu\Assessments\Assessor\Transformers;


use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Assessments\Assessor\Transformers\AssessmentQuestionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;

class AssessmentTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'assessmentQuestion',
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



    public function transform(Assessment $assessment)
    {
        return [
            'id' => $assessment->id,
            'title' => (string)$assessment->title,
            'introduction' => (string)$assessment->introduction,
            'start_at' => (string)Carbon::parse($assessment->start_at)->format('Y-m-d'),
            'end_at' => (string)Carbon::parse($assessment->end_at)->format('Y-m-d'),
            'start_time' => (string)Carbon::parse($assessment->start_at)->format('H:i'),
            'end_time' => (string)Carbon::parse($assessment->end_at)->format('H:i'),
            'assessor_type' => (string)$assessment->assessor_type,
            'assessor_label' => trans('app.' . $assessment->assessor_type),
            'assessee_type' => (string)$assessment->assessee_type,
            'assessee_label' => trans('app.' . $assessment->assessee_type),
            "has_general_comment" => (boolean) $assessment->has_general_comment,
        ];
    }



    public function includeAssessmentQuestion(Assessment $assessment)
    {
        if(isset($this->params['startAssessment']) && isset($this->params['question'])){

            return $this->collection(
                $this->params['question'], new AssessmentQuestionTransformer($assessment,$this->params),
                ResourceTypesEnums::ASSESSMENT_QUESTION
            );
        }

    }

    public function includeActions(Assessment $assessment)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.assessments.assessor.get.assessees.index', ['assessment' => $assessment->id]),
            'label' => trans('assessment.assessee'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_ASSESSMENT_AESSESSEES
        ];

        if(count($actions) > 0 ) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
