<?php

namespace App\OurEdu\LandingPage\Transformers;

use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;

class SubjectTransformer extends TransformerAbstract
{
    public function transform(Subject $subject): array
    {
        return [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
        ];
    }
}
