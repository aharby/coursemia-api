<?php

namespace App\OurEdu\Courses\Parent\Controllers;

use Illuminate\Http\Request;
use App\OurEdu\Users\Models\Student;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Transformers\CourseTransformer;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Courses\Transformers\CourseListTransformer;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use App\OurEdu\Courses\Repository\CourseRepositoryInterface;

class CourseApiController extends BaseApiController
{
    public function __construct(
        CourseRepositoryInterface $courseRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->courseRepository = $courseRepository;
        $this->userRepository = $userRepository;

        $this->user = Auth::guard('api')->user();
        $this->middleware('type:parent');
    }

    public function getIndex(Request $request)
    {
        $courses = $this->courseRepository->paginageFilteredCourses();

        return $this->transformDataModInclude($courses, ['instructor', 'sessions', 'subject', 'actions'], new CourseListTransformer(), ResourceTypesEnums::COURSE);
    }
}
