<?php

namespace App\OurEdu\GeneralQuizzes\Homework\Instructor\UseCases\InstructorHomeworkUseCases;

use App\OurEdu\BaseNotification\Jobs\CalculateGeneralAverageGradesAndCountStudentsJob;
use App\OurEdu\BaseNotification\Jobs\FinishGeneralQuizStudentJob;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Homework\Jobs\NotificationHomeworkStudentsJob;
use App\OurEdu\GeneralQuizzes\Jobs\ValidateGeneralQuizMarkJob;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use App\OurEdu\Users\UserEnums;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UpdateHomeworkUseCase implements UpdateHomeworkUseCaseInterface
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


    public function updateHomeWork(int $homeworkId, $data): array
    {
        $homework = $this->generalQuizRepo->findOrFail($homeworkId);

        $validationErrors = $this->validateEditHomework($homework, $data);

        if ($validationErrors) {
            return $validationErrors;
        }
        $this->generalQuizRepo->setGeneralQuiz($homework)->update($data->toArray());
        $homework = $this->generalQuizRepo->getGeneralQuiz();
        $this->generalQuizRepo->saveGeneralQuizClassrooms($homework, $data->classrooms->pluck('id')->toArray());
        $this->generalQuizRepo->saveGeneralQuizSections($homework, $data->subject_sections);
        if (isset($data->students) && count($homework->classrooms) == 1) {
            $this->generalQuizRepo->saveGeneralQuizStudents($homework, $data->students->pluck('id')->toArray());
        } else {
            $this->generalQuizRepo->saveGeneralQuizStudents($homework, []);
        }
        $useCase['homework'] = $homework;
        $useCase['status'] = 200;
        $useCase['meta'] = [
            'message' => trans('api.Updated Successfully')
        ];
        return $useCase;
    }

    private function validateEditHomework($homework, $data)
    {
        if (count($homework->studentsAnswered)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.cant edit attended general quiz');
            $useCase['title'] = 'cant edit attended homework';
            return $useCase;
        }
        $classroomIds = $data->classrooms->pluck('id')->toArray();
        $branches = [];
        if (auth()->user()->type == UserEnums::SCHOOL_INSTRUCTOR) {
            $branches[] = auth()->user()->branch_id;
        } elseif (auth()->user()->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            if (!is_null(auth()->user()->branch)) {
                $branches[] = auth()->user()->branch_id;
            } else {
                $branches = auth()->user()->branches->pluck('id')->toArray();
            }
        }

        $homeworkValidateDate = GeneralQuiz::query()
            ->where('id', '!=', $homework->id)
            ->whereIn("branch_id", $branches)
            ->where('subject_id', "=", $data->subject_id)
            ->where("quiz_type", "=", GeneralQuizTypeEnum::HOMEWORK)
            ->whereHas('classrooms', function ($query) use ($data, $classroomIds) {
                $query->whereIn('id', $classroomIds);
            })
            ->where(function ($query) use ($data) {
                $start_at = $data->start_at;
                $end_at = $data->end_at;
                $query->whereBetween('start_at', [$start_at, $end_at])
                    ->orWhereBetween('end_at', [$start_at, $end_at])
                    ->orWhere(function ($nestedQuery) use ($start_at, $end_at) {
                        $nestedQuery->where("start_at", "<=", $end_at)
                            ->where("end_at", ">=", $start_at);
                    });
            })->first();

        if (!$homework->is_active) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.homeWork is deactivated');
            $useCase['title'] = 'homework is deactivated';

            return $useCase;
        }

        if ($homeworkValidateDate) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.there is an existed homework for same subject at same time', [
                "from" => \Carbon\Carbon::parse($homeworkValidateDate->start_at)->format("d/m h:i a"),
                "to" => \Carbon\Carbon::parse($homeworkValidateDate->end_at)->format("d/m h:i a")
            ]);
            $useCase['title'] = 'classrooms has homework at same time';
            return $useCase;
        }

        if (!isset($data->classrooms) || $data->classrooms->count() == 0) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('generalQuiz.classroom is required');
            $useCase['title'] = 'classrooms is required';
            return $useCase;
        }

        $branchClassroomsIds = $this->classroomRepo->getBranchClassroomsByIds(
            $classroomIds,
            $homework->branch_id,
            $data->subject_id
        );

        $invalidClassrooms = array_values(array_diff($classroomIds, $branchClassroomsIds));

        if (count($invalidClassrooms) > 0) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.invalid branch classrooms');
            $useCase['title'] = 'Invalid Classrooms';
            return $useCase;
        }

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
    }

    public function publishHomework(GeneralQuiz $homework, bool $forece = false): array
    {
        $validationErrors = $this->validateHomeworkPublishing($homework);

        if (!$forece and $validationErrors) {
            return $validationErrors;
        }

        $updateData['published_at'] = Carbon::now();

        if (Carbon::parse($homework->start_at)->diffInMinutes(Carbon::now()) < 30) {

            if ((new Carbon($homework->start_at))->isFuture()) {
                NotificationHomeworkStudentsJob::dispatch($homework)->delay((new Carbon($homework->start_at)));
            } else {
                NotificationHomeworkStudentsJob::dispatch($homework);
            }
            $updateData['is_notified'] = 1;
        }

        $this->generalQuizRepo->setGeneralQuiz($homework)->update($updateData);


        ValidateGeneralQuizMarkJob::dispatch($homework);

        $useCase["status"] = 200;

        return $useCase;
    }


    private function validateHomeworkPublishing(GeneralQuiz $homework)
    {
        $quizType = trans('general_quizzes.' . $homework->quiz_type);
        if (!$homework->is_active) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.inactive general quiz', [
                'quiz_type' => $quizType
            ]);
            $useCase['title'] = 'homework is inactive';
            return $useCase;
        }

        if ($homework->published_at) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.Homework Already Published');
            $useCase['title'] = 'Homework published before';
            return $useCase;
        }

        if ($homework->start_at <= Carbon::now()) {
            $useCase['status'] = 422;

            $useCase['detail'] = trans('general_quizzes.general quiz start time Passed', [
                'quiz_type' => trans('gneeral_quizzes.' . $homework->quiz_type)
            ]);

            $useCase['title'] = 'homework start time passed';
            return $useCase;
        }


        $questionCount = $homework->questions()->count();

        if ($questionCount < 1) {
            $useCase['status'] = 422;

            $useCase['detail'] = trans(
                'general_quizzes.general quiz not have any Question, you have to add the questions before publishing',
                [
                    'quiz_type' => trans('gneeral_quizzes.' . $homework->quiz_type)
                ]
            );

            $useCase['title'] = 'homework Not have questions';
            return $useCase;
        }
    }

    public function deactivateHomework(GeneralQuiz $homework)
    {
        if (!$homework->is_active) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('general_quizzes.Already Deactivated');
            $useCase['title'] = 'homework already deactivated';
            return $useCase;
        }

        $this->generalQuizRepo->setGeneralQuiz($homework)->update(["is_active" => false]);
        FinishGeneralQuizStudentJob::dispatch($homework);
        CalculateGeneralAverageGradesAndCountStudentsJob::dispatch($homework)->delay(Carbon::now()->addMinutes(5));
        $useCase["status"] = 200;

        return $useCase;
    }
}
