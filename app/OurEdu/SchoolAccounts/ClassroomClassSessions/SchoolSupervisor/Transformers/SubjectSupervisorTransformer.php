<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\SchoolSupervisor\Transformers;

use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;

class SubjectSupervisorTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [

    ];


    /**
     * @param Subject $subject
     * @return array
     */
    public function transform(Subject $subject)
    {

        $transformedData = [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
            'educational_system' => (string)($subject->educationalSystem->name ?? ''),
            'academical_years' => (string)($subject->academicalYears->title ?? ''),
            'grade_class' => (string)($subject->gradeClass->title ?? ''),
            'subject_image' => (string)imageProfileApi($subject->image, 'large'),
            'color' => (string)$subject->color,
            'subject_library_text' => $subject->subject_library_text,
        ];

        return $transformedData;
    }


}
