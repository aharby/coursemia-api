<?php

namespace App\OurEdu\LandingPage\Repositories;

use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LandingPageRepository implements LandingPageRepositoryInterface
{
    public function listCourses()
    {
        return  Course::query()
            ->whereHas('instructor')
            ->where('is_active',1)
            ->with(["instructor", "subject"])
//            ->where("end_date", ">=", Carbon::now()->format("Y-m-d"))
//            ->where("start_date", "<=", Carbon::now()->format("Y-m-d"))
            ->inRandomOrder()
            ->take(20)
            ->get();
    }

    public function getStatistics()
    {
        $data['students'] =  User::query()
                ->where('type', UserEnums::STUDENT_TYPE)
                ->whereHas('student',function ($query){
                    $query->whereNull(['classroom_id']);
                })->count();
        $data['instructors'] =  User::query()
            ->where('type', UserEnums::INSTRUCTOR_TYPE)
            ->active()
            ->count();
        $data['courses'] = Course::query()
            ->whereHas('instructor')
            ->where('is_active',1)
            ->count();

        return [$data];
    }
}
