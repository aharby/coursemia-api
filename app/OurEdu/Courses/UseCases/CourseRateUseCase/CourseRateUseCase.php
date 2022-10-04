<?php

namespace App\OurEdu\Courses\UseCases\CourseRateUseCase;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Repository\CourseRepositoryInterface;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use App\OurEdu\Users\User;

class CourseRateUseCase implements CourseRateUseCaseInterface
{
    private $studentRepo;
    private $courseRepo;
    private $liveSessionRepo;

    public function __construct(
        StudentRepositoryInterface $studentRepo,
        CourseRepositoryInterface $courseRepository
    ) {
        $this->studentRepo = $studentRepo;
        $this->courseRepo = $courseRepository;
    }

    /**
     * @param $data
     * @param int $courseId
     * @param User $user
     * @return array
     */
    public function rateCourse($data, int $courseId, User $user): array
    {
        $returnArr = [];
        $course = $this->courseRepo->findOrFail($courseId);
        if ($course) {
            if (!is_student_subscribed_to_course($course)) {
                $returnArr['status'] = 422;
                $returnArr['detail'] = trans('course.cant rate course you are not subscribed on');
                $returnArr['title'] = 'cant rate course you are not subscribed on';
                return $returnArr;
            }
            // rated before
            if ($course->ratings()->where('user_id', $user->id)->exists()) {
                $returnArr['status'] = 422;
                $returnArr['detail'] = trans('api.You already rated this course');
                $returnArr['title'] = 'You already rated this course';
                return $returnArr;
            } else {
                // first time to rate
                $rate = $course->ratingUnique([
                    'rating'    =>  $data->rating,
                    'comment'    =>  $data->comment,
                    'instructor_id'    =>  $course->instructor_id,
                ], $user);
                if ($rate) {
                    $returnArr['status'] = 200;
                    return $returnArr;
                }
            }
        }
        $returnArr['status'] = 500;
        $returnArr['detail'] = trans('app.Oopps Something is broken');
        $returnArr['title'] = trans('app.Oopps Something is broken');
        return $returnArr;
    }
}
