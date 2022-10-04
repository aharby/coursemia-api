<?php
namespace App\OurEdu\Courses\Discussion\Instructor\Middleware;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\CourseDiscussion;
use App\OurEdu\Courses\Models\CourseDiscussionComment;
use App\OurEdu\Users\UserEnums;
use Closure;
use Illuminate\Support\Facades\Auth;

class IsCourseInstructor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->route('courseDiscussion'))
        {
                $courseDiscussion = $request->route('courseDiscussion');

                if (!($courseDiscussion instanceof CourseDiscussion))
                {
                    $courseDiscussion = CourseDiscussion::query()->findOrFail($courseDiscussion);
                }

                $course = $courseDiscussion->course;
        }
        elseif ($request->route('course'))
        {
            $course = $request->route('course');

            if (!($course instanceof Course))
            {
                $course = Course::query()->findOrFail($course);
            }
        }
        elseif ($request->route('courseDiscussionComment'))
        {
            $courseDiscussionComment = $request->route('courseDiscussionComment');
            if (!($courseDiscussionComment instanceof CourseDiscussionComment))
            {
                $courseDiscussionComment = CourseDiscussionComment::query()->findOrFail($courseDiscussionComment);
            }
            $course = $courseDiscussionComment->discussions->course;
        }

        if ($request->user()->type === UserEnums::INSTRUCTOR_TYPE)
        {
            if ($course->instructor_id !== $request->user()->id)
            {
                return abort(403);
            }
        }

        return $next($request);
    }
}
