<?php

namespace App\OurEdu\GeneralQuizzes\Student\Transformers;

use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;

class SubjectTransformer extends TransformerAbstract
{

    public function transform(Subject $subject): array
    {
        return [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
            'subject_image' => (string) imageProfileApi($subject->image, 'small'),
            'educational_system' => (string)($subject->educationalSystem->name ?? ''),
            'academical_years' => (string)($subject->academicalYears->title ?? ''),
            'grade_class' => (string)($subject->gradeClass->title ?? ''),
        ];
    }
}
