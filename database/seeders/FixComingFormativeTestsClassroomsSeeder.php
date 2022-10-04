<?php

namespace Database\Seeders;

use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;

class FixComingFormativeTestsClassroomsSeeder extends Seeder
{
    private ClassroomRepositoryInterface $classroomRepository;

    public function __construct(ClassroomRepositoryInterface $classroomRepository)
    {
        $this->classroomRepository = $classroomRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $formativeTests = GeneralQuiz::query()
            ->where('quiz_type', '=', GeneralQuizTypeEnum::FORMATIVE_TEST)
            ->where('start_at', '>', now())
            ->get();

        foreach ($formativeTests as $formativeTest) {
            $schoolAdmin = $formativeTest->creator;
            $userSchools = $schoolAdmin
                ->schoolAdminAssignedSchools()
                ->pluck("school_accounts.id")
                ->toArray();

            $branches = SchoolAccountBranch::query()
                ->whereIn('school_account_id', $userSchools)
                ->whereHas(
                    'educationalSystems',
                    function (Builder $educationalSystem) use ($formativeTest) {
                        $educationalSystem->where('educational_systems.id', '=', $formativeTest->educational_system_id);
                    }
                )
                ->pluck("id")
                ->toArray();

            $classrooms = $this->classroomRepository->getClassroomsByBranchesAndGradeClasses(
                $branches,
                $formativeTest->grade_class_id,
                null,
                $formativeTest->educational_system_id
            );

            $formativeTest->classrooms()->sync($classrooms);
        }
    }
}
