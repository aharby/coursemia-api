<?php

namespace App\OurEdu\Assessments\AssessmentManager\Transformers;

use App\OurEdu\Assessments\Models\AssessmentPointsRate;
use League\Fractal\TransformerAbstract;

class AssessmentPointsRateTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(AssessmentPointsRate $assessment_rate)
    {

        $transformedData = [
            'id' => (int)$assessment_rate->id,
            'min_points' => (int)$assessment_rate->min_points,
            'max_points' => (int)$assessment_rate->max_points,
            'rate' => (string)$assessment_rate->rate
        ];

        return $transformedData;
    }
}
