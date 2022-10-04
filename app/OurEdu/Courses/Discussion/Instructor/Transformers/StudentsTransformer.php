<?php

namespace App\OurEdu\Courses\Discussion\Instructor\Transformers;

use App\OurEdu\Users\Models\Student;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Courses\Models\Course;

class StudentsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = ['actions'];

    public function __construct(private Course $course)
    {
    }

    public function transform(Student $student)
    {
        $transformedData = [
            'id' => $student->id,
            'name' => $student->user?->name,
            'isActive' => (bool)$student->pivot->is_discussion_active,
            'profile_picture' => (string)imageProfileApi($student->user?->profile_picture),
        ];

        return $transformedData;
    }

    public function includeActions(Student $student)
    {
        $actions = [];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.discussions.instructor.toggleStudentActivation', [
                'course'=> $this->course->id,
                'student' => $student->id,
            ]),
            'label' =>$student->pivot->is_discussion_active ?  trans('app.inactivate') : trans('app.activate') ,
            'method' => 'GET',
            'key' => APIActionsEnums::ACTIVATE_STUDENT_COURSE
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
