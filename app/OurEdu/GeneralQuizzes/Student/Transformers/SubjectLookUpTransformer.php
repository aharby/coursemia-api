<?php

namespace App\OurEdu\GeneralQuizzes\Student\Transformers;

use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;

class SubjectLookUpTransformer extends TransformerAbstract
{
    public function transform(Subject $subject): array
    {
        return [
            "id" => $subject->id,
            "name" => $subject->name,
        ];
    }
}
