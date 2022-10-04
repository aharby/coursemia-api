<?php

namespace App\OurEdu\Courses\UseCases\CourseRateUseCase;

use App\OurEdu\Users\User;

interface CourseRateUseCaseInterface
{
    /**
     * @param  $data
     * @param int $courseId
     * @param User $user
     * @return array
     */
    public function rateCourse($data, int $courseId, User $user): array ;
}
