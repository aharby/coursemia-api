<?php

namespace App\OurEdu\LandingPage\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LandingPage\Repositories\LandingPageRepositoryInterface;
use App\OurEdu\LandingPage\Transformers\CoursesTransformer;
use App\OurEdu\LandingPage\Transformers\StatisticsTransformer;

class LandingPageController extends BaseApiController
{
    private LandingPageRepositoryInterface $landingPageRepository;

    /**
     * @param LandingPageRepositoryInterface $landingPageRepository
     */
    public function __construct(LandingPageRepositoryInterface $landingPageRepository)
    {
        $this->landingPageRepository = $landingPageRepository;
    }

    public function courses()
    {
        $courses = $this->landingPageRepository->listCourses();

        return $this->transformDataModInclude($courses, "", new CoursesTransformer(), ResourceTypesEnums::COURSE);
    }

    public function statistics()
    {
        $data = $this->landingPageRepository->getStatistics();

        return $this->transformDataModIncludeItem($data,'',new StatisticsTransformer(),ResourceTypesEnums::STATISTICS);
    }
}
