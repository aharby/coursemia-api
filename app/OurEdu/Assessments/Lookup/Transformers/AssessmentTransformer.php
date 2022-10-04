<?php

namespace App\OurEdu\Assessments\Lookup\Transformers;

use App\OurEdu\Assessments\Models\Assessment;
use League\Fractal\TransformerAbstract;

class AssessmentTransformer extends TransformerAbstract
{
    public function transform(Assessment $assessment)
    {

        return [
            'id' => (int) $assessment->id,
            'title' => (string) $assessment->title,

        ];
    }
}
