<?php

namespace App\OurEdu\Courses\Repository;

use App\OurEdu\Courses\Enums\CourseSessionEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\CourseDiscussion;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use  App\OurEdu\Courses\Models\SubModels\CourseStudent;
use OwenIt\Auditing\Models\Audit;

class CourseRepository implements CourseRepositoryInterface
{
    private $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function setCourse($course)
    {
        $this->course = $course;

        return $this;
    }


    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return $this->course->jsonPaginate(env('PAGE_LIMIT', 20));
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
        return $this->course->latest()->with('sessions')->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    public function paginateWhere(array $where, $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $this->course->where($where)->jsonPaginate($perPage, ['*'], $pageName, $page = null);
    }

    /**
     * @param array $data
     * @return Course
     */
    public function create(array $data): Course
    {
        return $this->course->create($data);
    }

    /**
     * @param array $data
     * @return Course|null
     */
    public function update(array $data): ?Course
    {
        if ($this->course->update($data)) {
            return $this->course->find($this->course->id);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return $this->course->delete();
    }

    public function findOrFail(int $id): ?Course
    {
        return Course::findOrFail($id);
    }

    public function getCourseSessions($id)
    {
        return CourseSession::latest()->where('course_id', $id)->jsonPaginate();
    }

    public function getCoursesRelatedToStudent(Student $student, $paginate = true)
    {
        $subjects = Subject::where('academical_years_id', $student->academical_year_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('country_id', auth()->user()->country_id)
            ->where('grade_class_id', $student->class_id)
            ->pluck('id')->toArray();

        $courses = Course::query()
            ->where(function ($query) use ($subjects) {
                $query->whereIn('subject_id', $subjects);
                $query->orWhere('subject_id', null);
            })
            ->where('end_date', '>', date("Y-m-d"))
            ->where('is_active', 1)
            ->with([
                'sessions' => function ($query) {
                    $query->where('status', '!=', CourseSessionEnums::CANCELED);
                },
                'instructor',
                'subject'
            ]);

        if (request()->has('subscribed')) {
            if (request()->boolean('subscribed')) {
                $courses->whereHas('students', function ($q) use ($student) {
                    $q->where('student_id', "=", $student->id);
                });
            } else {
                $subscripedCourses = (clone $courses)->whereHas('students', function ($q) use ($student) {
                    $q->where('student_id', '=', $student->id);
                })->pluck('id')->toArray();
                $courses->whereNotIn(
                    'id',
                    $subscripedCourses
                );
            }
        }
        $courses->latest();
        if ($paginate) {
            return $courses->jsonPaginate();
        }
        return $courses->get();
    }

    public function getAllStudentCourses(Student $student)
    {
        $subjects = Subject::where('academical_years_id', $student->academical_year_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('country_id',auth()->user()->country_id)
            ->where('grade_class_id',$student->class_id)
            ->pluck('id')->toArray();

        $courses = Course::latest()
            ->where(function ($query) use ($subjects){
                $query->whereIn('subject_id',$subjects);
                $query->orWhere('subject_id', null);
            })
            ->where('is_active',1)
            ->with(['sessions' =>  function ($query){
              $query->where('status','!=',CourseSessionEnums::CANCELED);
            }, 'instructor', 'subject']);

        if (request()->has('subscribed')) {
            if (request()->boolean('subscribed')) {
                $courses->whereHas('students', function ($q) use ($student) {
                    $q->where('student_id', "=", $student->id);
                });
            } else {
                   $courses->whereDoesntHave("students", function ($q) use ($student){
                    $q->where('student_id', "=", $student->id);
                });
            }
        }
        return $courses->jsonPaginate();
    }

    public function paginageFilteredCourses()
    {
        return Course::latest()
            ->where('is_active', true)
            ->when(request('name'), function ($q) {
                return $q->where('name', 'LIKE', '%' . request('name') . '%');
            })
            ->whereHas('subject', function ($query) {
                return $query->when(request('educational_system_id'), function ($q) {
                    return $q->where('educational_system_id', request('educational_system_id'));
                })->when(request('grade_class_id'), function ($q) {
                    return $q->where('grade_class_id', request('grade_class_id'));
                })->when(request('country_id'), function ($q) {
                    return $q->where('country_id', request('country_id'));
                });
            })
            ->with('sessions', 'instructor', 'subject')
            ->paginate(env('PAGE_LIMIT', 20));
    }


    /**
     * @return bool
     */
    public function makeCoursesOutOfDate(): bool
    {
        return $this->course
            ->whereNotNull('end_date')
            ->where('end_date', '<', date("Y-m-d"))
            ->where('out_of_date', 0)
            ->update(['out_of_date' => 1]);
    }

    /**
     * function add course created sessions to Audit logs
     * @param $course created course
     * @return void
     */
    public function addSessionsToLog(Course $course): void
    {
        $auditRow = Audit::where('auditable_type', Course::class)
            ->where('auditable_id', $course->id)->where('event', 'created')
            ->first();
        $auditRowData = $auditRow->new_values;

        // resolve instructor name and subject name
        if ($auditRowData['instructor_id']) {
            $auditRowData['instructor_name'] = User::find($auditRowData['instructor_id'])->name;
        }
        if ($auditRowData['subject_id']) {
            $auditRowData['subject_name'] = Subject::find($auditRowData['subject_id'])->name;
        }
        $auditRowData['sessions'] = array();
        foreach ($course->sessions()->get() as $session) {
            array_push($auditRowData['sessions'], $session->first()->toArray());
        }
        $auditRow->update(['new_values' => $auditRowData]);
    }

    /**
     * @param User $instructor
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCoursesByInstructor(User $instructor)
    {
        $courses = Course::query()
            ->where('is_active','=',1)
            ->where('instructor_id', "=", $instructor->id)
            ->where("end_date", ">=", Carbon::now()->toDateString())
            ->orderByDesc("start_date");

        if (request()->filled("type")) {
            $courses->where("type", "=", request()->query->get("type"));
        }

        return $courses->paginate(env("PAGE_LIMIT", 20));
    }

    /**
     * create new user discussion
     * @param array $data
     * @return CourseDiscussion
     */
    public function createDiscussion(array $data): CourseDiscussion
    {
        return CourseDiscussion::create($data);
    }

    public function getStudentUnsubscribedCourses(Student $student)
    {
        return Course::query()
            ->whereDoesntHave("students", function ($q) use ($student) {
                $q->where('student_id', "=", $student->id);
            })
            ->where('is_active', 1)
            ->where('is_top_qudrat', 1)
            ->with([
                'sessions' => function ($query) {
                    $query->where('status', '!=', CourseSessionEnums::CANCELED);
                },
                'instructor',
                'subject'
            ])
            ->latest()
            ->jsonPaginate();
    }
    public function getStudentSubscribedAndUnsubscribedCourses()
    {
        return Course::query()
            ->where('is_active', 1)
            ->where('is_top_qudrat', 1)
            ->with([
                'sessions' => function ($query) {
                    $query->where('status', '!=', CourseSessionEnums::CANCELED);
                },
                'instructor',
                'subject'
            ])
            ->latest()
            ->jsonPaginate();
    }
    public function getStudentSubscribedCourses(Student $student)
    {
        return Course::query()
            ->whereHas("students", function ($q) use ($student) {
                $q->where('student_id', "=", $student->id);
            })
            ->where('is_active', 1)
            ->with([
                'sessions' => function ($query) {
                    $query->where('status', '!=', CourseSessionEnums::CANCELED);
                },
                'instructor',
                'subject'
            ])
            ->latest()
            ->jsonPaginate();
    }

    public function getStudentsSubscribedCourse(Course $course)
    {
        return Student::query()
           ->whereHas("courses", function ($q) use ($course) {
            $q->where('course_id', "=", $course->id);
            })
            ->get();
    }
}
