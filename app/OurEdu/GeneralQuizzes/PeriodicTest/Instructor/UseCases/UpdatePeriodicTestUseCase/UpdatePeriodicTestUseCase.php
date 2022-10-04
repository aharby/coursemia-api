<?php

namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\UseCases\UpdatePeriodicTestUseCase;

use App\OurEdu\BaseNotification\Jobs\CalculateGeneralAverageGradesAndCountStudentsJob;
use App\OurEdu\BaseNotification\Jobs\FinishGeneralQuizStudentJob;
use App\OurEdu\GeneralQuizzes\Homework\Jobs\NotificationHomeworkStudentsJob;
use App\OurEdu\GeneralQuizzes\Jobs\ValidateGeneralQuizMarkJob;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UpdatePeriodicTestUseCase implements UpdatePeriodicTestUseCaseInterface
{

    private $generalQuizRepo;
    private $classroomRepo;
    private $studentRepo;
    private $user;

    public function __construct(
        GeneralQuizRepositoryInterface $generalQuizRepo,
        ClassroomRepositoryInterface $classroomRepo,
        StudentRepositoryInterface $studentRepo
    ) {
        $this->generalQuizRepo = $generalQuizRepo;
        $this->classroomRepo = $classroomRepo;
        $this->studentRepo = $studentRepo;
        $this->user = Auth::guard('api')->user();
    }


    public function updatePeriodicTest(int $periodicTestId, $data): array
    {
        $periodicTest = $this->generalQuizRepo->findOrFail($periodicTestId);

        $gradeClassClassroomsIds = $this->classroomRepo->getClassroomsByBranchAndGradeclass(
            $this->user->branch_id,
            $data->grade_class_id
        );

        $validationErrors = $this->validateUpdatePeriodicTest($periodicTest, $data, $gradeClassClassroomsIds);

        if ($validationErrors) {
            return $validationErrors;
        }
        $data['test_time'] = $data['test_time'] * 60;

        $this->generalQuizRepo->setGeneralQuiz($periodicTest)->update(($data->toArray()));

        $periodicTest = $this->generalQuizRepo->getGeneralQuiz();


        $classroomIds = isset($data->classrooms) ? $data->classrooms->pluck('id')->toArray() : [];
        $studentIds = isset($data->students) ? $data->students->pluck('id')->toArray() : [];

        $classroomsCount = count($classroomIds);

        // case one if there's no given classrooms
        if ($classroomsCount == 0) {
            //if there's a given students
            if (count($studentIds) > 0) {
                $this->generalQuizRepo->saveGeneralQuizStudents($periodicTest, $studentIds);
            } else {
                $this->generalQuizRepo->saveGeneralQuizClassrooms($periodicTest, $gradeClassClassroomsIds);
                $this->generalQuizRepo->saveGeneralQuizStudents($periodicTest, []);
            }
        } elseif ($classroomsCount > 0) {
            // case two if there's a given classrooms
            $this->generalQuizRepo->saveGeneralQuizClassrooms($periodicTest, $classroomIds);
            // if count of classroom is 1 and there's  a given students
            if ($classroomsCount == 1 && count($studentIds) > 0) {
                $this->generalQuizRepo->saveGeneralQuizStudents($periodicTest, $studentIds);
            } else {
                $this->generalQuizRepo->saveGeneralQuizStudents($periodicTest, []);
            }
        }

        $this->generalQuizRepo->saveGeneralQuizSections($periodicTest, $data->subject_sections);


        $useCase['periodicTest'] = $periodicTest;
        $useCase['status'] = 200;
        $useCase['meta'] = [
            'message' => trans('api.Updated Successfully')
        ];
        return $useCase;
    }


    private function validateUpdatePeriodicTest($periodicTest, $data, $gradeClassClassroomsIds)
    {
        if (count($periodicTest->studentsAnswered)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.cant edit attended general quiz');
            $useCase['title'] = 'cant edit attended quiz';
            return $useCase;
        }

        if (!$periodicTest->is_active) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.periodicTest is deactivated');
            $useCase['title'] = 'periodicTest is deactivated';

            return $useCase;
        }
        $classroomIds = isset($data->classrooms) ? $data->classrooms->pluck('id')->toArray() : [];

        if (count($classroomIds) > 0) {
            $invalidClassrooms = array_values(array_diff($classroomIds, $gradeClassClassroomsIds));
            if (count($invalidClassrooms) > 0) {
                $useCase['status'] = 422;
                $useCase['detail'] = trans('general_quizzes.invalid gradeclass classrooms');
                $useCase['title'] = 'Invalid Classrooms';
                return $useCase;
            }
        }

        // validate that the given  students belongs to the given classroom
        if (count($classroomIds) == 1 && (isset($data->students) && $data->students->count() > 0)) {
            $studentIds = $data->students->pluck('id')->toArray();
            $classroomsStudents = $this->studentRepo->getClassroomStudentsByUserIds($studentIds, $classroomIds);
            $invalidStudents = array_values(array_diff($studentIds, $classroomsStudents));
            if (count($invalidStudents) > 0) {
                $useCase['status'] = 422;
                $useCase['detail'] = trans('general_quizzes.Invalid students');
                $useCase['title'] = 'Invalid students';
                return $useCase;
            }
        }

        // if there is no given classrooms and there's students then validate that these students belongs to this grade class
        if (count($classroomIds) == 0 && (isset($data->students) && $data->students->count() > 0)) {
            $studentIds = $data->students->pluck('id')->toArray();
            $classroomsStudents = $this->studentRepo->getClassroomStudentsByUserIds(
                $studentIds,
                $gradeClassClassroomsIds
            );
            $invalidStudents = array_values(array_diff($studentIds, $classroomsStudents));
            if (count($invalidStudents) > 0) {
                $useCase['status'] = 422;
                $useCase['detail'] = trans('general_quizzes.Invalid students');
                $useCase['title'] = 'Invalid students';
                return $useCase;
            }
        }
    }

    public function publishPeriodicTest(GeneralQuiz $periodicTest): array
    {
        $validationErrors = $this->validatePublishPeriodicTest($periodicTest);

        if ($validationErrors) {
            return $validationErrors;
        }

        $updateData['published_at'] = Carbon::now();

        if (Carbon::parse($periodicTest->start_at)->diffInMinutes(Carbon::now()) < 30) {

            if ((new Carbon($periodicTest->start_at))->isFuture()) {
                NotificationHomeworkStudentsJob::dispatch($periodicTest)->delay((new Carbon($periodicTest->start_at)));
            } else {
                NotificationHomeworkStudentsJob::dispatch($periodicTest);
            }
            $updateData['is_notified'] = 1;
        }

        $this->generalQuizRepo->setGeneralQuiz($periodicTest)->update($updateData);
        ValidateGeneralQuizMarkJob::dispatch($periodicTest);
        $useCase["status"] = 200;
        return $useCase;
    }


    private function validatePublishPeriodicTest(GeneralQuiz $periodicTest)
    {
        $quizType = trans('general_quizzes.' . $periodicTest->quiz_type);
        if (!$periodicTest->is_active) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.inactive general quiz', [
                'quiz_type' => $quizType
            ]);
            $useCase['title'] = 'periodicTest is inactive';
            return $useCase;
        }

        if ($periodicTest->published_at) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.Periodic test Already Published');
            $useCase['title'] = 'periodicTest already published';
            return $useCase;
        }

        if ($periodicTest->start_at >= Carbon::now()) {
            // $useCase['status'] = 422;
            // $useCase['detail'] = trans('general_quizzes.general quiz start time Passed',[
            //     'quiz_type'=>trans('gneeral_quizzes.'.$periodicTest->quiz_type)
            // ]);

            // $useCase['title'] = 'periodicTest start time passed';
            // return $useCase;
        }

        if ($periodicTest->end_at < Carbon::now()) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.quiz time has ended', [
                'quiz_type' => trans('gneeral_quizzes.' . $periodicTest->quiz_type)
            ]);

            $useCase['title'] = 'periodicTest time passed';
            return $useCase;
        }

        $questionCount = $periodicTest->questions()->count();

        if ($questionCount < 1) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans(
                'general_quizzes.general quiz not have any Question, you have to add the questions before publishing',
                [
                    'quiz_type' => trans('general_quizzes.' . $periodicTest->quiz_type)
                ]
            );
            $useCase['title'] = 'periodic test Not have questions';
            return $useCase;
        }
    }

    public function deactivatePeriodicTest(GeneralQuiz $periodicTest)
    {
        if (!$periodicTest->is_active) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.Already Deactivated');
            $useCase['title'] = 'periodicTest already deactivated';
            return $useCase;
        }

        $this->generalQuizRepo->setGeneralQuiz($periodicTest)->update(["is_active" => false]);
        FinishGeneralQuizStudentJob::dispatch($periodicTest);
        CalculateGeneralAverageGradesAndCountStudentsJob::dispatch($periodicTest)->delay(Carbon::now()->addMinutes(5));
        $useCase["status"] = 200;

        return $useCase;
    }

    public function deactivateHomework(GeneralQuiz $periodicTest)
    {
        if (!$periodicTest->is_active) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.Already Deactivated');
            $useCase['title'] = 'homework already deactivated';
            return $useCase;
        }

        $this->generalQuizRepo->setGeneralQuiz($periodicTest)->update(["is_active" => false]);
        FinishGeneralQuizStudentJob::dispatch($periodicTest);
        CalculateGeneralAverageGradesAndCountStudentsJob::dispatch($periodicTest)->delay(Carbon::now()->addMinutes(5));
        $useCase["status"] = 200;

        return $useCase;
    }
}
