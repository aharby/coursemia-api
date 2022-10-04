<?php

namespace App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CloneFormativeUseCase;

use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\AddQuestionBankToGeneralQuizInterface;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAdmin\FormativeTest\Job\CloneQuestionsJob;
use App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CloneQuestionsUseCase\CloneQuestionsUseCase;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CloneFormativeUseCase implements CloneFormativeUseCaseInterface
{
    public function __construct(
        private AddQuestionBankToGeneralQuizInterface $addQuestionBankToGeneralQuiz,
        private GeneralQuizRepositoryInterface $generalQuizRepository,
        private ClassroomRepositoryInterface $classroomRepo
    )
    {
    }

    public function clone(GeneralQuiz $generalQuiz, $data)
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

        $subject = Subject::query()
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

        $useCase = [];
        $additionalData = [];
        $additionalData['subject_sections'] = $subjectMainSections;
        $additionalData['start_at'] = $data['from'] . ' ' . $data['from_time'];
        $additionalData['end_at'] = $data['to'] . ' ' . $data['to_time'];
        $additionalData['quiz_type'] = GeneralQuizTypeEnum::FORMATIVE_TEST;
        $additionalData['branch_id'] = null;
        $additionalData['grade_class_id'] = $data['grade_class_id'];
        $additionalData['test_time'] = $data['test_time'] * 60;
        $additionalData['school_account_id'] = null;

        $actualStartAt = Carbon::parse($additionalData['start_at']);
        $actualEndAt = Carbon::parse($additionalData['end_at']);
        $diffTimeInMinutes = $actualEndAt->diffInMinutes($actualStartAt, true);

        if ((int)$data['test_time'] > $diffTimeInMinutes) {
            $useCase['status'] = 422;
            $useCase['message'] = trans('formative_tests.time exceed');

            return $useCase;
        }

        if ($additionalData['end_at'] < $additionalData['start_at']) {
            $useCase['status'] = 422;
            $useCase['message'] = trans('formative_tests.end Date before start');

            return $useCase;
        }

        if ($additionalData['start_at'] < now()) {
            $useCase['status'] = 422;
            $useCase['message'] = trans('formative_tests.start time before now');

            return $useCase;
        }


        $formativeTest = $this->generalQuizRepository->create(array_merge($data, $additionalData));

        if (count($subjectMainSections)) {
            $this->generalQuizRepository->saveGeneralQuizSections($formativeTest, $subjectMainSections);
        }

        $this->generalQuizRepository->saveGeneralQuizClassrooms($formativeTest, $classroomsIds);

        CloneQuestionsJob::dispatch($formativeTest, $generalQuiz->questions, Auth::user());

        $useCase['formative_test'] = $formativeTest;
        $useCase ['message'] = trans('formative_tests.formativeTest_cloned');
        $useCase['status'] = 200;

        return $useCase;
    }
}
