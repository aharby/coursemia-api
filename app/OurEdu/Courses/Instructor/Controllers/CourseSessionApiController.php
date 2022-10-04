<?php

namespace App\OurEdu\Courses\Instructor\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Instructor\Transformers\CourseSessionTransformer;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Repository\CourseSessionRepositoryInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CourseSessionApiController extends BaseApiController
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var CourseSessionRepositoryInterface
     */
    private $courseSessionRepository;

    private $user;

    public function __construct(
        CourseSessionRepositoryInterface $courseSessionRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->courseSessionRepository = $courseSessionRepository;
        $this->userRepository = $userRepository;

        $this->user = Auth::guard('api')->user();
    }


    public function listAvailable()
    {
        $instructor = $this->user->instructor;
        $courseSession = $this->courseSessionRepository->getRelatedCourseSessionsForInstructor($this->user);
        return $this->transformDataModInclude($courseSession, ['course' , 'subject'],
            new CourseSessionTransformer(), ResourceTypesEnums::COURSE_SESSION);
    }

    public function courseSessions(Course $course)
    {
        $courseSession = $this->courseSessionRepository->getCourseSessionsForInstructor($this->user, $course);

        return $this->transformDataModInclude($courseSession, ['course' , 'subject',"recordedSessions"],
            new CourseSessionTransformer(), ResourceTypesEnums::COURSE_SESSION);
    }


}
