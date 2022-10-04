<?php

namespace App\OurEdu\Assessments\AssessmentManager\Transformers;

use App\OurEdu\Assessments\Models\Assessment;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use Carbon\Carbon;

class AssessmentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
        'assessors',
        'assessees',
        'assessmentResultViewerTypes',
        'assessmentPointsRates'
    ];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(Assessment $assessment)
    {

        $transformedData = [
            'id' => (int)$assessment->id,
            'title' => (string)$assessment->title,
            'introduction' => (string)$assessment->introduction,
            'is_active' => (bool)$assessment->is_active,
            'start_at' => (string)Carbon::parse($assessment->start_at)->format('Y-m-d'),
            'end_at' => (string)Carbon::parse($assessment->end_at)->format('Y-m-d'),
            'start_time' => (string)Carbon::parse($assessment->start_at)->format('H:i'),
            'end_time' => (string)Carbon::parse($assessment->end_at)->format('H:i'),
            'assessor_type' => (string)$assessment->assessor_type,
            'assessee_type' => (string)$assessment->assessee_type,
            'has_general_comment' => (bool)$assessment->has_general_comment,
            'assessor_type_is_general' => (boolean)$assessment->assessor_type_is_general,
            'assessee_type_is_general' => (boolean)$assessment->assessee_type_is_general,
            'assessment_viewer_type_is_general' => (boolean)$assessment->assessment_viewer_type_is_general,
        ];

        return $transformedData;
    }

    public function includeAssessors(Assessment $assessment)
    {
        return $this->collection(
            $assessment->assessors,
            new UserTransformer,
            ResourceTypesEnums::USER
        );
    }

    public function includeAssessees(Assessment $assessment)
    {
        return $this->collection(
            $assessment->assessees,
            new UserTransformer,
            ResourceTypesEnums::USER
        );
    }

    public function includeAssessmentResultViewerTypes(Assessment $assessment)
    {
        return $this->collection(
            $assessment->resultViewerTypes,
            new ResultViewTypesTransformer(),
            ResourceTypesEnums::ASSESSMENT_VIEWER_TYPE
        );
    }

    public function includeAssessmentPointsRates(Assessment $assessment)
    {
        return $this->collection(
            $assessment->rates,
            new AssessmentPointsRateTransformer(),
            ResourceTypesEnums::ASSESSMENT_POINTS_RATE
        );
    }


    public function includeActions(Assessment $assessment)
    {
        $actions = [];
        // edit
        if(is_null($assessment->published_at) and (!$assessment->published_before or $assessment->start_at > now())) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.assessments.assessment-manager.put.edit', ['assessmentId' => $assessment->id]),
                'label' => trans('app.edit_assessment'),
                'method' => 'PUT',
                'key' => APIActionsEnums::EDIT_ASSESSMENT
            ];

        }

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.assessments.assessment-manager.delete.delete', ['assessment' => $assessment->id]),
            'label' => trans('app.delete_assessment'),
            'method' => 'DELETE',
            'key' => APIActionsEnums::DELETE_ASSESSMENT
        ];

        if (is_null($assessment->published_at) and $assessment->end_at > now()) {
            // publish
            $label = trans('app.publish_assessment');

            if ($assessment->published_before) {
                $label = trans('app.continue_publish_assessment');
            }

            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.assessments.assessment-manager.post.publish', ['assessment' => $assessment->id]),
                'label' => $label,
                'method' => 'POST',
                'key' => APIActionsEnums::PUBLISH_ASSESSMENT
            ];
        }

        if (!is_null($assessment->published_at) and $assessment->end_at > now()) {
            // unpublish
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.assessments.assessment-manager.post.unpublish', ['assessment' => $assessment->id]),
                'label' => trans('app.unpublish_assessment'),
                'method' => 'POST',
                'key' => APIActionsEnums::UNPUBLISH_ASSESSMENT
            ];
        }

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.assessments.assessment-manager.get.view', ['assessment' => $assessment->id]),
            'label' => trans('app.show_assessment'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_ASSESSMENT
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.assessments.assessment-manager.get.preview', ['assessment' => $assessment->id]),
            'label' => trans('app.preview_assessment'),
            'method' => 'GET',
            'key' => APIActionsEnums::PREVIEW_ASSESSMENT
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.assessments.assessment-manager.post.cloneAssessment', ['assessment' => $assessment->id]),
            'label' => trans('app.clone_assessment'),
            'method' => 'POST',
            'key' => APIActionsEnums::CLONE_ASSESSMENT
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
