<?php

namespace App\OurEdu\LearningPerformance\Parent\Transformers\ListsTransformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Transformers\CourseRatingsTransformer;
use App\OurEdu\Subjects\SME\Transformers\SubjectTransformer;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\Transformers\UserTransformer;
use League\Fractal\TransformerAbstract;

class CoursesListTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
        'instructor',
        'ratings',
        //      'subject',

    ];

    private $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function transform(Course $course)
    {
        $currencyCode = $this->student->educationalSystem->country->currency ?? '';
        return [
            'id' => (int)$course->id,
            'name' => (string)$course->name,
            'course-type' => (string)$course->type,
            'start_date' => $course->start_date,
            'subject_title' => $course->subject?->name,
            'subscription_cost' =>(float) $course->subscription_cost . " " . $currencyCode,
            'picture' => (string) imageProfileApi($course->picture,'large'),
            'is_subscribe' =>  is_student_subscribed_to_course($course , $this->student->user),
            'total_sessions_count' => $course->sessions ? count($course->sessions) : 0,
            'total_sessions_attended' => $this->calculateAttendantSession($course) ?? 0,
            'progress'=>calculateCourseProgress($course, $this->student->user),
            'medium_picture' => (string) imageProfileApi($course->medium_picture, 'large'),
            'small_picture' => (string) imageProfileApi($course->small_picture, 'large'),
            'apple_price' => $course->apple_price . " " . $currencyCode
        ];
    }

    public function includeActions(Course $course)
    {
        $actions = [];
        if (!is_student_subscribed_to_course($course, $this->student->user)) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.parent.subscriptions.post.courseSubscripe',
                    ['id' => $course->id, 'studentId' => $this->student->id]
                ),
                'label' => trans('app.Subscribe'),
                'method' => 'POST',
                'key' => APIActionsEnums::COURSE_SUBSCRIBE
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeInstructor(Course $course)
    {
        if ($course->instructor) {
            return $this->item($course->instructor, new UserTransformer(), ResourceTypesEnums::USER);
        }
    }

    public function includeRatings(Course $course)
    {
        return $this->item($course, new CourseRatingsTransformer(), ResourceTypesEnums::RATING);
    }

    private function calculateAttendantSession($course)
    {
        return $this->student->user->VCRSessionsPresence()->whereHas('vcrSession',function ($query) use($course){
            $query->where('course_id',$course->id);
        })->count();
    }
}
