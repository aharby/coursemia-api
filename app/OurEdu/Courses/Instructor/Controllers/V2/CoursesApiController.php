<?php

namespace App\OurEdu\Courses\Instructor\Controllers\V2;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Instructor\Transformers\CourseSessionTransformer;
use App\OurEdu\Courses\Instructor\Transformers\V2\CourseTransformer;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Repository\CourseSessionRepositoryInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CoursesApiController extends BaseApiController
{
    /**
     * @var CourseSessionRepositoryInterface
     */
    private $courseSessionRepository;

    private $user;

    public function __construct(
        CourseSessionRepositoryInterface $courseSessionRepository
    ) {
        $this->courseSessionRepository = $courseSessionRepository;
        $this->user = Auth::guard('api')->user();
    }


    public function index(Course $course)
    {
        $courseSession = $this->courseSessionRepository->getCourseSessionsForInstructor($this->user, $course);
        $params['courseSessions'] = $courseSession;
        $params['user'] = $this->user;
        return $this->transformDataModInclude($course, ['sessions' , 'subject'],
            new CourseTransformer($params), ResourceTypesEnums::COURSE);
    }
}
