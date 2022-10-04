<?php

namespace App\OurEdu\Courses\Discussion\Instructor\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\Courses\Discussion\Instructor\Transformers\StudentsTransformer;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\CourseDiscussion;
use App\OurEdu\Courses\Models\CourseDiscussionComment;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Discussion\Instructor\Middleware\IsCourseInstructor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CourseDiscussionController extends BaseApiController
{
    public function __construct()
    {
        $this->middleware(IsCourseInstructor::class);
    }


    public function listStudents(Course $course)
    {
        return $this->transformDataModInclude($course->students, '', new StudentsTransformer($course), ResourceTypesEnums::STUDENT);
    }

    public function toggleStudentActivation(Course $course, Student $student)
    {
        $student->courses()->updateExistingPivot($course->id, ['is_discussion_active'=> DB::raw('NOT is_discussion_active')]);
        $isActive = $course?->students()->where('id', $student->id)->first() ?
        $course->students()->where('id', $student->id)->first(
                  )->pivot->is_discussion_active : null;
       
        $meta = $isActive ? trans('discussions.student Active') : trans('discussions.student not Active');

        return response()->json(['meta' => $meta]);
    }

    public function deleteDiscussion(CourseDiscussion $courseDiscussion): JsonResponse
    {
        $courseDiscussion->delete();

        return response()->json(['meta' => trans('api.Deleted Successfully')]);
    }

    public function deleteDiscussionComment(CourseDiscussionComment $courseDiscussionComment): JsonResponse
    {
        $courseDiscussionComment->delete();

        return response()->json(['meta' => trans('api.Deleted Successfully')]);
    }

}
