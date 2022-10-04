<?php

namespace App\OurEdu\SchoolAdmin\FormativeTest\UseCases\UpdateFormativeTestUseCase;

use App\OurEdu\GeneralQuizzes\Homework\Jobs\NotificationHomeworkStudentsJob;
use App\OurEdu\GeneralQuizzes\Jobs\ValidateGeneralQuizMarkJob;
use App\OurEdu\Subjects\Models\Subject;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;

class UpdateFormativeTestUseCase implements UpdateFormativeTestUseCaseInterface
{
    private $generalQuizRepo;
    private $classroomRepo;
    private $user;

    public function __construct(
        GeneralQuizRepositoryInterface $generalQuizRepo,
        ClassroomRepositoryInterface $classroomRepo,
    )
    {
        $this->generalQuizRepo = $generalQuizRepo;
        $this->classroomRepo = $classroomRepo;
    }

    public function updateFormativeTest(array $data, GeneralQuiz $formativeTest): array
    {
        DB::beginTransaction();
        try {
            $this->user = Auth::user();
            $userSchools = $this->user
                ->schoolAdminAssignedSchools()
                ->pluck("school_accounts.id")
                ->toArray();

            $branches = SchoolAccountBranch::query()
                ->whereIn("school_account_id", $userSchools)
                ->whereHas(
                    "educationalSystems",
                    function (Builder $educationalSystem) use ($data) {
                        $educationalSystem->where("educational_systems.id", "=", $data['educational_system_id']);
                    }
                )
                ->pluck("id")
                ->toArray();

            $classroomsIds = $this->classroomRepo->getClassroomsByBranchesAndGradeClasses(
                $branches,
                $data['grade_class_id'],
                null,
                $data['educational_system_id']
            );

            $subject= Subject::query()
                ->where("id", '=', $data['subject_id'])
                ->firstOrFail();

            $subjectMainSections = $subject->subjectFormatSubject()
                ->whereNull("parent_subject_format_id")
                ->pluck("id")
                ->toArray();

            if (!count($subjectMainSections)) {
                $useCase['status'] = 422;
                $useCase['message'] = trans('formative_tests.the subject must contain Sections');

                return $useCase;
            }

            $additionalData = [];
            $additionalData['start_at'] = $data['from'] .' '. $data['from_time'];
            $additionalData['end_at'] = $data['to'] .' '. $data['to_time'];
            $additionalData['quiz_type'] = GeneralQuizTypeEnum::FORMATIVE_TEST;
            $additionalData['branch_id'] = null;
            $additionalData['grade_class_id'] = $data['grade_class_id'];
            $additionalData['test_time'] = $data['test_time']*60;
            $additionalData['school_account_id'] = null;

            $actualStartAt = Carbon::parse($additionalData['start_at']);
            $actualEndAt   = Carbon::parse($additionalData['end_at']);
            $differenceTimeInMinutes  = $actualEndAt->diffInMinutes($actualStartAt, true);

            if ((int)$data['test_time'] >  $differenceTimeInMinutes) {
                $useCase['status'] = 422;
                $useCase['message'] = trans('formative_tests.time exceed');

                return $useCase;
            }

            if ($additionalData['end_at'] <  $additionalData['start_at']) {
                $useCase['status'] = 422;
                $useCase['message'] = trans('formative_tests.end Date after start');

                return $useCase;
            }

            if ($additionalData['start_at'] < now()) {
                $useCase['status'] = 422;
                $useCase['message'] = trans('formative_tests.end time or start time less than now');

                return $useCase;
            }

            $this->generalQuizRepo->setGeneralQuiz($formativeTest)->update(array_merge($data, $additionalData));

            if (count($subjectMainSections)) {
                $this->generalQuizRepo->saveGeneralQuizSections($formativeTest, $subjectMainSections);
            }

            $this->generalQuizRepo->saveGeneralQuizClassrooms($formativeTest, $classroomsIds);

            DB::commit();
            $useCase =[];
            $useCase['formative test'] = $formativeTest;
            $useCase ['message'] = trans('formative_tests.formativeTest_updated');
            $useCase['status'] = 200;

            return $useCase;
        } catch (Exception $ex) {
            DB::rollback();
            $useCase ['message'] = trans('app.Something went wrong');
            $useCase['status'] = 500;
            return $useCase;
        }
    }

    public function publishFormativeTest(GeneralQuiz $formativeTest): array
    {
        $validationErrors = $this->validateFormativeTestPublish($formativeTest);
         $useCase = [];

        if ($validationErrors) {
            return $validationErrors;
        }
        $status = $formativeTest->published_at ? null : Carbon::now();
        $updateData = [];
        $updateData["published_at"] = $status;

        $this->generalQuizRepo->setGeneralQuiz($formativeTest)->update($updateData);
        ValidateGeneralQuizMarkJob::dispatch($formativeTest);

        if ($formativeTest->published_at) {
            if (\Carbon\Carbon::parse($formativeTest->start_at)->diffInMinutes(Carbon::now()) < 30) {

                if ((new Carbon($formativeTest->start_at))->isFuture()) {
                    NotificationHomeworkStudentsJob::dispatch($formativeTest)->delay((new Carbon($formativeTest->start_at)));
                } else {
                    NotificationHomeworkStudentsJob::dispatch($formativeTest);
                }
                $updateData['is_notified'] = 1;
            }

            $useCase["status"] = 200;
            $useCase["message"] =  trans('formative_tests.formativeTest_published');
        }

        if (!$formativeTest->published_at) {
            $useCase["status"] = 200;
            $useCase["message"] =  trans('formative_tests.formativeTest_unpublished');
        }

        return $useCase;
    }

    private function validateFormativeTestPublish(GeneralQuiz $formativeTest)
    {
        $useCase =[];
        if ($formativeTest->start_at <= Carbon::now() and !$formativeTest->published_at) {
            $useCase['status'] = 422;
            $useCase['message'] = trans(
                'formative_tests.formative test start time Passed',
                [
                    'quiz_type' => trans('formative_tests.' . $formativeTest->quiz_type)
                ]
            );
            return $useCase;
        }

        if ($formativeTest->questions()->count() < 1) {
            $useCase['status'] = 422;
            $useCase['message'] = trans(
                'formative_tests.formative test have questions',
                [
                    'quiz_type' => trans('formative_tests.' . $formativeTest->quiz_type)
                ]
            );

            return $useCase;
        }
    }
}
