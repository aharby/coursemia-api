<?php

namespace App\OurEdu\Assessments\AssessmentManager\Transformers;

use App\OurEdu\Assessments\Models\AssessmentCategory;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use League\Fractal\TransformerAbstract;

class AssessmentCategoryTransformer extends TransformerAbstract
{
    protected array $defaultIncludes=['actions'];

    public function transform(AssessmentCategory $assessmentCategory)
    {
        return [
            'id' => (int) $assessmentCategory->id,
            'assessment_id' => (int) $assessmentCategory->assessment_id,
            'title' => (string) $assessmentCategory->title,
        ];
    }

    public function includeActions(AssessmentCategory $assessmentCategory)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.assessments.assessment-manager.categories.update', [
                'assessmentCategory' => $assessmentCategory->id,
            ]),
            'label' => trans('app.edit'),
            'method' => 'PUT',
            'key' => APIActionsEnums::ASSESSMENT_CATEGORY_EDIT
        ];
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.assessments.assessment-manager.categories.delete', [
                'assessmentCategory' => $assessmentCategory->id,
            ]),
            'label' => trans('app.Delete'),
            'method' => 'DELETE',
            'key' => APIActionsEnums::ASSESSMENT_CATEGORY_DELETE
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
