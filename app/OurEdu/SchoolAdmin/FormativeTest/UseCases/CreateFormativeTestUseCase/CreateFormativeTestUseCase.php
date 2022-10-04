<?php

namespace App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CreateFormativeTestUseCase;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;

class CreateFormativeTestUseCase implements CreateFormativeTestUseCaseInterface
{
    private $generalQuizRepo;
    private $classroomRepo;
    private $user;

    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepo, ClassroomRepositoryInterface $classroomRepo,)
    {
        $this->generalQuizRepo = $generalQuizRepo;
        $this->classroomRepo = $classroomRepo;
    }

    public function createFormativeTest(array $data): array
    {
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

        $useCase =[];
        $additionalData = [];
        $additionalData['subject_sections'] = $subjectMainSections;
        $additionalData['start_at'] = $data['from'] .' '. $data['from_time'];
        $additionalData['end_at'] = $data['to'] .' '. $data['to_time'];
        $additionalData['quiz_type'] = GeneralQuizTypeEnum::FORMATIVE_TEST;
        $additionalData['branch_id'] = null;
        $additionalData['grade_class_id'] = $data['grade_class_id'];
        $additionalData['test_time'] = $data['test_time']*60;
        $additionalData['school_account_id'] = null;

        $actualStartAt = Carbon::parse($additionalData['start_at']);
        $actualEndAt   = Carbon::parse($additionalData['end_at']);
        $diffTimeInMinutes = $actualEndAt->diffInMinutes($actualStartAt, true);

        if ((int)$data['test_time'] > $diffTimeInMinutes) {
            $useCase['status'] = 422;
            $useCase['message'] = trans('formative_tests.time exceed');

            return $useCase;
        }

        if ($additionalData['end_at'] <  $additionalData['start_at']) {
            $useCase['status'] = 422;
            $useCase['message'] = trans('formative_tests.end Date before start');

            return $useCase;
        }

        if ($additionalData['start_at'] < now()) {
            $useCase['status'] = 422;
            $useCase['message'] = trans('formative_tests.start time before now');

            return $useCase;
        }


        $formativeTest = $this->generalQuizRepo->create(array_merge($data, $additionalData));

        if (count($subjectMainSections)) {
            $this->generalQuizRepo->saveGeneralQuizSections($formativeTest, $subjectMainSections);
        }

        $this->generalQuizRepo->saveGeneralQuizClassrooms($formativeTest, $classroomsIds);

        $useCase['formative_test'] = $formativeTest;
        $useCase ['message'] = trans('formative_tests.formativeTest_created');
        $useCase['status'] = 200;

        return $useCase;
    }
}
