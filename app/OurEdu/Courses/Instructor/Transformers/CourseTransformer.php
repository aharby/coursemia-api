<?php

namespace App\OurEdu\Courses\Instructor\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\SME\Transformers\SubjectTransformer;
use App\OurEdu\Courses\Transformers\CourseSessionTransformer;
use App\OurEdu\Courses\Transformers\CourseRatingsTransformer;
use Carbon\Carbon;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Models\Course;
use League\Fractal\TransformerAbstract;

class CourseTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];

    protected array $availableIncludes = [
        "actions",
        "ratings"
    ];

    public function __construct()
    {

    }

    public function transform(Course $course)
    {
        $curencyCode = $course->subject->educationalSystem->country->currency ?? '';
        $transformedData = [
            'id' => (int) $course->id,
            'name' => (string) $course->name,
            'description' => (string) $course->description,
            'course-type' => (string) $course->type,
            'instructor_id' => (int) $course->instructor_id,
            'instructor_name' => (string) $course->instructor->name ?? "",
            'subject_id' => (int) $course->subject_id,
            'subscription_cost' =>(float) $course->subscription_cost . " " . $curencyCode,
            'start_date' => (string) $course->start_date,
            'end_date' => (string) $course->end_date,
            'is_ended' => $course->end_date < now()->format('Y-m-d') ? true : false,
            'picture' => (string) imageProfileApi($course->picture, 'large'),
            'is_active' => (boolean) $course->is_active,
            'number_of_sessions'    =>  $course->sessions()->count(),
            "subject_name" => (string)($course->subject->name ?? ""),
            "educational_system" => (string)($course->subject->educationalSystem->name ?? ""),
            "educational_term" => (string)($course->subject->educationalTerm->title ?? ""),
            "educational_grade" => (string)($course->subject->gradeClass->title ?? ""),
            'medium_picture' => (string) imageProfileApi($course->medium_picture, 'large'),
            'small_picture' => (string) imageProfileApi($course->small_picture, 'large')
        ];


        return $transformedData;
    }

    /**
     * @param Course $course
     * @return array
     */
    public function includeActions(Course $course){
        $actions =[];

        if (Carbon::now()->isBetween(Carbon::parse($course->start_date), Carbon::parse($course->end_date))) {
            $actions[] = [
            'endpoint_url' => buildScopeRoute('api.general-quizzes.course-homework.instructor.post.create_course_homework', ['course' => $course->id]),
            'label' => trans('app.Create') .' '.trans('app.homework'),
            'method' => 'POST',
            'key' => APIActionsEnums::CREATE_COURSE_HOMEWORK
        ];
        }

        if ($course->homeworks->count()) {
            $actions[] = [
            'endpoint_url' => buildScopeRoute('api.general-quizzes.course-homework.instructor.get.courses.list', ['course' => $course->id]),
            'label' => trans('general_quizzes.Course Homeworks'),
            'method' => 'GET',
            'key' => APIActionsEnums::COURSE_HOMEWORK_LIST
        ];
        }


        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.instructor.courseSessions.list.course.sessions', ['course' => $course->id]),
            'label' => trans('app.Show Course'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_COURSE_SESSION
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.discussions.index', ['course' => $course->id]),
            'label' => trans('courses.discussion.list discussions'),
            'key' => APIActionsEnums::LIST_COURSE_DISCUSSIONS,
            'method' => 'POST'
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.instructor.courses.getStudents', ['course' => $course->id]),
            'label' => trans('app.students'),
            'method' => 'GET',
            'key' => APIActionsEnums::LIST_COURSE_STUDENTS
        ];

        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
    public function includeRatings(Course $course)
    {
        return $this->item($course, new CourseRatingsTransformer(), ResourceTypesEnums::RATING);
    }
}
