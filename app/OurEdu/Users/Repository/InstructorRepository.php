<?php


namespace App\OurEdu\Users\Repository;


use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\VCRSchedules\Models\VCRRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InstructorRepository implements InstructorRepositoryInterface
{
    use Filterable;
    protected $instructor;

    public function __construct(Instructor $instructor)
    {
        $this->model = $instructor;
    }

    public function all(array $filters = []): LengthAwarePaginator
    {
        $model = $this->applyFilters($this->model, $filters);
        return $model->orderBy('id','DESC')->paginate(env('PAGE_LIMIT', 10));
    }

    public function create(array $data): ?Instructor
    {
        return Instructor::create($data);
    }

    public function findOrFail(int $id): ?Instructor
    {
        return Instructor::findOrFail($id);
    }

    public function update(Instructor $instructor, array $data): bool
    {
        return $instructor->update($data);
    }

    public function delete(Instructor $instructor): bool
    {
        return $instructor->delete();
    }

    public function getInstructorByUserId(int $userId): ?Instructor
    {
        return Instructor::where('user_id', $userId)->firstOrFail();
    }

    public function paginate(array $filters = [],$perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $model = $this->applyFilters(new Instructor(), $filters);
        $perPage = $perPage ?? env('PAGE_LIMIT', 20);
        return $model->orderBy('id', 'DESC')->paginate($perPage, ['*'], $pageName, $page = null);

    }

    public function pluck(): Collection
    {
        return $this->model->with('user')->get()->pluck('user.name', 'user.id');
    }

    public function studentCoursesCountNumber($id): int
    {
        $count = 0;
        // count number of sutdent of courses
        $courses = Course::where('instructor_id',$id)->get();
        foreach ($courses as $course) {
            $count += $course->students()->count();

            // count number of students who joined scheduled sessions
            foreach ($course->sessions() as $session) {
                $count += $session->participants()->count();;
            }
        }

        // count number of sutdent who requests vcr sessions
        $count += VCRRequest::where('instructor_id',$id)->groupBy('student_id')->count();
        return $count;
    }


    public function export(array $filters = []): Collection
    {
        $model = $this->applyFilters($this->model, $filters);
        return $model->orderBy('id','DESC')->get();
    }

    public function getInstructorSessions($instructorId)
    {
        $sessions = CourseSession::whereHas('course', function ($q)  use ($instructorId){
            $q->withoutGlobalScopes()
             ->where('instructor_id', $instructorId)
            ->whereNull('deleted_at');
        });

        return $sessions;

    }

    public function getSessionsCourse($courseId)
    {
        $sessions = CourseSession::whereHas('course', function ($q)  use ($courseId){
            $q->withoutGlobalScopes()
             ->where('course_id', $courseId)
            ->whereNull('deleted_at');
        })->get();

        return $sessions;

    }


}
