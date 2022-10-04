<?php

namespace App\OurEdu\StaticPages\Transformers\DistinguishedStudents;


use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;

class SubjectTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [

    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;

    }

    public function transform(Subject $subject)
    {
        $curencyCode = $subject->educationalSystem->country->currency ?? '';
        $transformedData = [
            'id' => $subject->id,
            'name' => (string)$subject->name,
            'educational_system' => (string)($subject->educationalSystem->name ?? ''),
            'academical_years' => (string)($subject->academicalYears->title ?? ''),
            'grade_class' => (string)($subject->gradeClass->title ?? ''),
            'subscription_cost' => $subject->subscription_cost . " " . $curencyCode,
            'subject_image' => (string) imageProfileApi($subject->image, 'large'),
            'color' => (string)$subject->color,
            'subject_library_text' => $subject->subject_library_text,
        ];
        return $transformedData;
    }

}
