<?php

namespace App\OurEdu\Courses\Repository;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\Task;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\Users\User;
use Illuminate\Pagination\LengthAwarePaginator;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Courses\Enums\CourseSessionEnums;
class CourseSessionRepository implements CourseSessionRepositoryInterface
{
    private $courseSession;

    public function __construct(CourseSession $courseSession)
    {
        $this->courseSession = $courseSession;
    }

    public function setSession($courseSession)
    {
        $this->courseSession = $courseSession;

        return $this;
    }


    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return $this->courseSession->jsonPaginate(env('PAGE_LIMIT', 20));
    }

    /**
     * @param null $perPage
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->courseSession->latest()->with('sessions')->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    public function paginateWhere(array $where, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->courseSession->where($where)->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param array $data
     * @return Course|null
     */
    public function update(array $data): ?CourseSession
    {
        if ($this->courseSession->update($data)) {
            return $this->courseSession->find($this->courseSession->id);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return $this->courseSession->delete();
    }

    public function findOrFail($id): ?CourseSession
    {
        return $this->courseSession->findOrFail($id);
    }

    public function getRelatedCourseSessionsForInstructor(User $instructor) : LengthAwarePaginator {
        return $this->courseSession->whereHas('course' , function ($course) use ($instructor){
            $course->where('instructor_id' , $instructor->id);
        })->orderBy('date' , 'desc')
        ->orderBy('start_time' , 'desc')
        ->jsonPaginate();
    }

    public function getCourseSessionsForInstructor(User $instructor, Course $course) : LengthAwarePaginator {

        $sessions =  CourseSession::query()
            ->with(['course.instructor', 'course.subject'])
            ->whereHas('course' , function ($course) use ($instructor) {
                $course->where('instructor_id' , $instructor->id);
            })
            ->where('status', CourseSessionEnums::ACTIVE)
            ->orderBy('date')
            ->orderBy('start_time')
            ->with('course')
            ->where('course_id', "=", $course->id);

        $sessions = $sessions->jsonPaginate();

        return $sessions;
    }

    public function getRelatedSessionForStudent(Student $student)
    {
        $subscribedCourses = Course::whereHas('students', function($query) use ($student){
            $query->where('student_id', $student->id);
                })->pluck('id')->toArray();
         
        $sessions =  CourseSession::query()
            ->whereDate('date', '>=', now()->toDateString())
            ->where('status','!=',CourseSessionEnums::CANCELED)
            ->whereIn('course_id', $subscribedCourses)
            ->with('course')
            ->orderBy('end_time', 'desc')
            ->get()->filter(function($item){
                return now()->between($item->sessionStartTime, $item->sessionEndTime);
            })->first();

        return $sessions;

    }
}
