<?php

namespace App\OurEdu\Assessments\AssessmentManager\Transformers;

use App\OurEdu\Assessments\Models\AssessmentViewerType;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use League\Fractal\TransformerAbstract;

class ResultViewTypesTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = ['users'];

    public function transform(AssessmentViewerType $assessmentViewerType)
    {

        return [
            'id' => (int)$assessmentViewerType->id,
            'user_type' => (string)$assessmentViewerType->user_type,
        ];
    }

    public function includeUsers(AssessmentViewerType $assessmentViewerType)
    {
        return $this->collection(
            $assessmentViewerType->assessment
                ->resultViewers()
                ->where('type',$assessmentViewerType->user_type)->cursor(),
            new UserTransformer,
            ResourceTypesEnums::USER
        );
    }
}
