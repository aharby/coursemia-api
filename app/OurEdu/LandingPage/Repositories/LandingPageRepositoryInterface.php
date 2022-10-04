<?php

namespace App\OurEdu\LandingPage\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LandingPageRepositoryInterface
{
    public function listCourses();

    public function getStatistics();
}
