<?php

namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\UseCases;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Homework\Jobs\NotificationHomeworkStudentsJob;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\UseCases\UpdateCourseHomeworkUseCaseInterface;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UpdateCourseHomeworkUseCase implements UpdateCourseHomeworkUseCaseInterface
{

    private $generalQuizRepo;
    private $user;

    public function __construct(
        GeneralQuizRepositoryInterface $generalQuizRepo,
        ClassroomRepositoryInterface $classroomRepo,
        StudentRepositoryInterface $studentRepo
    )
    {
        $this->generalQuizRepo = $generalQuizRepo;
        $this->user = Auth::guard('api')->user();
    }

    public function updateHomeWork(GeneralQuiz $homework, $data, $course): array
    {
        $validationErrors = $this->validateEditHomework($homework, $data, $course);

        if ($validationErrors) {
            return $validationErrors;
        }
        $this->generalQuizRepo->setGeneralQuiz($homework)->update($data->toArray());
        $homework = $this->generalQuizRepo->getGeneralQuiz();

        $this->generalQuizRepo->saveGeneralQuizStudents(
            $homework,
            $course->students->pluck('user.id')->unique()->toArray()
        );

        $useCase['homework'] = $homework;
        $useCase['status'] = 200;
        $useCase['meta'] = [
            'message' => trans('api.Updated Successfully')
        ];
        return $useCase;
    }

    private function validateEditHomework(GeneralQuiz $homework, $data, $course)
    {
        if ($homework->studentsAnswered()->count()) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.cant edit attended general quiz');
            $useCase['title'] = 'cant edit attended homework';
            return $useCase;
        }

            if (isset($data['start_at']) and 
        Carbon::parse($data['start_at'])->notEqualTo(Carbon::parse($homework->start_at)) and 
        !is_null($homework->published_at)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.cant edit_start_at');
            $useCase['title'] = 'cant edit_start_at';
            return $useCase;
        }

        if (Carbon::now()->toDateString() > $course->end_date) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.cant edit courseHomeWork on ended Course');
            $useCase['title'] = 'cant edit courseHomework on ended course';
            return $useCase;
        }
    
    }

    public function publishCourseHomeWork(GeneralQuiz $courseHomework): array
    {
        $validationErrors = $this->validateCourseHomeworkPublish($courseHomework);
        if ($validationErrors) {
            return $validationErrors;
        }

        $useCase = [];
        $status = $courseHomework->published_at ? null : \Illuminate\Support\Carbon::now();
        $updateData = [];
        $updateData["published_at"] = $status;
        if ($status) {
            if (\Carbon\Carbon::parse($courseHomework->start_at)->diffInMinutes(Carbon::now()) < 30) {
                if ((new Carbon($courseHomework->start_at))->isFuture()) {
                    NotificationHomeworkStudentsJob::dispatch($courseHomework)->delay((new Carbon($courseHomework->start_at)));
                } else {
                    NotificationHomeworkStudentsJob::dispatch($courseHomework);
                }
                $updateData['is_notified'] = 1;
            }

            $courseHomework->update($updateData);

            $useCase["status"] = 200;
            $useCase["message"] =  trans('general_quizzes.Homework Published');
        }

        if ($courseHomework->published_at and !$status) {
            $courseHomework->update($updateData);
            $useCase["status"] = 200;
            $useCase["message"] =  trans('general_quizzes.HomeWork unpublished');

        }

        return $useCase;
    }

    public function deleteCourseHomeWork(GeneralQuiz $courseHomework): array
    {
        $validationErrors = $this->ValidateCourseHomeWorkDelete($courseHomework);
        $useCase = [];

        if ($validationErrors) {
            return $validationErrors;
        }
        $useCase = [];
        $courseHomework->delete();
        $useCase['status'] = 200;

        return $useCase;
    }

    private function ValidateCourseHomeWorkDelete(GeneralQuiz $courseHomework)
    {
        $useCase = [];
        if($courseHomework->studentsAnswered()->count()) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.Homework have students');
            $useCase['title'] = 'can not delete Homework have students';

            return $useCase;

        }
    }

    private function validateCourseHomeworkPublish(GeneralQuiz $courseHomework)
    {
        $useCase =[];
        if ($courseHomework->start_at <= Carbon::now() and !$courseHomework->published_at) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.Homework start time Passed');
            $useCase['title'] = 'HomeWork start time Passed';

            return $useCase;
        }

        if ($courseHomework->published_at and $courseHomework->studentsAnswered()->count()) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.Homework have students');
            $useCase['title'] = 'Can not unPublish homework that have students';

            return $useCase;
        }

        if ($courseHomework->questions()->count() < 1) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.Homework have questions');
            $useCase['title'] = 'Homework have questions';

            return $useCase;
        }

        if (Carbon::now()->toDateString() > $courseHomework->course->end_date) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.cant publish courseHomeWork on ended Course');
            $useCase['title'] = 'cant publish courseHomework on ended course';
            return $useCase;
        }
    }
}
