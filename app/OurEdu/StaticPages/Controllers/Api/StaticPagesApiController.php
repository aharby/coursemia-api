<?php

namespace App\OurEdu\StaticPages\Controllers\Api;

use App\OurEdu\Users\User;
use Illuminate\Http\Request;
use App\OurEdu\Users\UserEnums;
use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\StaticPages\Enums\StaticPagesEnum;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\StaticPages\Transformers\StaticPageTransformer;
use App\OurEdu\StaticBlocks\Transformers\StaticBlockTransformer;
use App\OurEdu\StaticPages\Transformers\InstructorTransformer;
use App\OurEdu\StaticPages\Transformers\ListInstructorsTransformer;
use App\OurEdu\StaticPages\Repository\StaticPagesRepositoryInterface;
use App\OurEdu\Courses\Models\Course;
class StaticPagesApiController extends BaseApiController
{
    private $staticPagesRepository;
    private $userRepository;

    public function __construct(
        StaticPagesRepositoryInterface $staticPagesRepository,
        UserRepositoryInterface $userRepository,
    )
    {
        $this->staticPagesRepository = $staticPagesRepository;
        $this->userRepository = $userRepository;
    }

    public function getStaticPage(Request $request, $pageSlug, $blockSlug = null)
    {
        try {
            $data = $this->staticPagesRepository->getPageBySlug($pageSlug, $blockSlug);
            // if data returned is not a page
            if ($data->page_id) {
                return $this->transformDataModInclude($data, $request->include, new StaticBlockTransformer(), ResourceTypesEnums::STATIC_BLOCK);
            }
            if ($pageSlug == StaticPagesEnum::HOMEPAGE) {
                $request->include = $request->include.',distinguishedStudents';
            }
            return $this->transformDataModIncludeItem($data, $request->include, new StaticPageTransformer, ResourceTypesEnums::STATIC_PAGE);
        }catch (\Throwable $e) {
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function listAllInstructors()
    {
        $instructors = $this->userRepository->listUsersByType(UserEnums::INSTRUCTOR_TYPE);
        return $this->transformDataModInclude($instructors, 'actions', new ListInstructorsTransformer(), ResourceTypesEnums::INSTRUCTOR);
    }

    public function showInstructor(User $instructor)
    {
        $courses = $this->getActiveCoursesByInstructor($instructor);

        return $this->transformDataModInclude($instructor, 'courses.actions', new InstructorTransformer($courses), ResourceTypesEnums::INSTRUCTOR);
    }

    private function getActiveCoursesByInstructor($instructor)
    {
        $courses = Course::query()
            ->where('instructor_id', "=", $instructor->id)
            ->where('end_date', '>=' , now()->toDateString())
            ->orderByDesc("start_date")
            ->paginate(9, '*', 'course-paginate');

    return $courses;

    }

}

