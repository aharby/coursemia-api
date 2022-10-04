<?php

namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\UseCases;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class CreateCourseHomeworkUsecase implements CreateCourseHomeworkUseCaseInterface
{

    private $generalQuizRepo;

    public function __construct(
        GeneralQuizRepositoryInterface $generalQuizRepo,
        ClassroomRepositoryInterface $classroomRepo,
        StudentRepositoryInterface $studentRepo
    )
    {
        $this->generalQuizRepo = $generalQuizRepo;
        $this->user = Auth::guard('api')->user();
    }

    public function createHomeWork($data, Course $course): array
    {
        $validationErrors = $this->validateCreateHomework($course);

        if ($validationErrors) {
            return $validationErrors;
        }
        $additionalData['quiz_type'] = GeneralQuizTypeEnum::COURSE_HOMEWORK;
        $additionalData['course_id'] = $course->id;

        $homework = $this->generalQuizRepo->create(array_merge($data->toArray(), $additionalData));

        $this->generalQuizRepo->saveGeneralQuizStudents(
            $homework,
            $course->students->pluck('user.id')->unique()->toArray()
        );

        $useCase['homework'] = $homework;
        $useCase['meta'] = [
            'message' => trans('general_quizzes.homework_created')
        ];
        $useCase['status'] = 200;
        return $useCase;
    }

    private function validateCreateHomework(Course $course)
    {
        if (Carbon::now()->toDateString() > $course->end_date) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.cant create courseHomeWork on ended Course');
            $useCase['title'] = 'cant create courseHomework on ended course';
            return $useCase;
        }
    }
}
