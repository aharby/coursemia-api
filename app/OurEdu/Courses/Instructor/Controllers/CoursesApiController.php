<?php


namespace App\OurEdu\Courses\Instructor\Controllers;



use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Instructor\Transformers\CourseTransformer;
use App\OurEdu\Courses\Instructor\Transformers\StudentsTransformer;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Repository\CourseRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CoursesApiController extends BaseApiController
{
    /**
     * @var CourseRepositoryInterface
     */
    private $courseRepository;

    /**
     * CoursesApiController constructor.
     * @param CourseRepositoryInterface $courseRepository
     */
    public function __construct(CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function index()
    {
        $user = Auth::user();
        $courses = $this->courseRepository->getCoursesByInstructor($user);

        return $this->transformDataModInclude($courses, "actions,ratings", new CourseTransformer(), ResourceTypesEnums::COURSE);
    }

    public function getStudents(Course $course)
    {
       $students =  $course->students()->paginate(env('PAGE_LIMIT', 20));

        return $this->transformDataMod($students, new StudentsTransformer(), ResourceTypesEnums::STUDENT);
    }
}
