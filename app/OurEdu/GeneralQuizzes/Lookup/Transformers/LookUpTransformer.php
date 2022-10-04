<?php


namespace App\OurEdu\GeneralQuizzes\Lookup\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Lookup\Transformers\GradeClassLookUpTransformer;
use App\OurEdu\GradeClasses\GradeClass;
use \App\OurEdu\GeneralQuizzes\Lookup\Transformers\SubjectLookUpTransformer;
use \App\OurEdu\GeneralQuizzes\Lookup\Transformers\ClassroomLookUptransformer;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Users\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use function GuzzleHttp\Psr7\str;

class LookUpTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [
        "gradeClasses",
        "classrooms",
        "subjects",
        "branches",
        'quiz_types'
    ];
    /**
     * @var array
     */
    private $params;
    /**
     * @var Authenticatable|null
     */
    private $user;

    /**
     * LookUpTransformer constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
        $this->user = Auth::guard("api")->user();
    }

    public function transform()
    {
        return [
            'id' => Str::uuid(),
        ];
    }

    public function includeGradeClasses(): \League\Fractal\Resource\Collection
    {
        $grades = GradeClass::query();

        if ($this->user && $this->user->type == UserEnums::SCHOOL_INSTRUCTOR) {
            $instructorGradeClasses = $this->user->schoolInstructorSubjects->pluck('grade_class_id')->toArray();
            $grades = $grades->whereIn('id', $instructorGradeClasses);
        }

        if ($this->user && $this->user->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            if (isset($this->param['branch_id'])) {
                $branchesIDs = [$this->param['branch_id']];
            } else {
                $branchesIDs = $this->user->branches()->get()->pluck("id")->toArray() ?? [];
            }

            $educationalSupervisorSubjects = $this->user->educationalSupervisorSubjects;
            $assignedGradeClassesIds =array_unique($educationalSupervisorSubjects->pluck('grade_class_id')->toArray());
            $grades = $grades->whereIn('id', $assignedGradeClassesIds)
                ->whereHas("branchEducationalSystemGradeClass.branchEducationalSystem.branch", function (Builder $branch) use ($branchesIDs) {
                    $branch->whereIn("id", $branchesIDs);
                });
        }

        return $this->collection(
            $grades->cursor(),
            new GradeClassLookUpTransformer(),
            ResourceTypesEnums::GRADE_CLASS
        );
    }

    public function includeClassrooms(): \League\Fractal\Resource\Collection
    {
        $classrooms = [];

        if ($this->user && $this->user->type == UserEnums::SCHOOL_INSTRUCTOR) {
            $classroomsIDs = $this->user->schoolInstructorSessions()->distinct()->pluck('classroom_id')->toArray();

            if (count($classroomsIDs)) {
                $classrooms = Classroom::query()->whereIn("id", $classroomsIDs);

                if (isset($this->params['grade_class_id'])) {
                    $classrooms->whereHas(
                        "branchEducationalSystemGradeClass",
                        function (Builder $branchEducationalSystemGradeClass) {
                            $branchEducationalSystemGradeClass->where("grade_class_id", "=", $this->params['grade_class_id']);
                        }
                    );
                }

                $classrooms = $classrooms->get();
            }
        }
        if ($this->user && $this->user->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            $classrooms = [];

            if (isset($this->param['branch_id'])) {
                $branchesIDs = [$this->param['branch_id']];
            } else {
                $branchesIDs = $this->user->branches()->get()->pluck("id")->toArray() ?? [];
            }

            if (count($branchesIDs)) {

                $educationalSupervisorSubjects = $this->user->educationalSupervisorSubjects;
                $assignedGradeClassesIds =array_unique($educationalSupervisorSubjects->pluck('grade_class_id')->toArray());
                $classes = GradeClass::query()->whereIn('id', $assignedGradeClassesIds)->pluck("id")->toArray();

                $classrooms = Classroom::query()->whereIn("branch_id", $branchesIDs);

                $classrooms->whereHas("branchEducationalSystemGradeClass.gradeClass", function (Builder $gradeClass) use ($classes) {
                    if (isset($this->param['grade_class_id'])) {
                        $gradeClass->where("id", "=", $this->param['grade_class_id']);
                    }

                    $gradeClass->whereIn("id", $classes);
                });

                $classrooms = $classrooms->get(['name', 'id']);
            }

            return $this->collection($classrooms, new \App\OurEdu\LookUp\Transformers\ClassroomLookUpTransformer(),
                ResourceTypesEnums::CLASSROOM);
        }

        return $this->collection(
            $classrooms,
            new ClassroomLookUptransformer(),
            ResourceTypesEnums::CLASSROOM
        );
    }

    public function includeSubjects(): \League\Fractal\Resource\Collection
    {
        $subjects = [];

        if ($this->user && $this->user->type == UserEnums::SCHOOL_INSTRUCTOR) {
            $subjects = $this->user->schoolInstructorSubjects();

            if (isset($this->params['grade_class_id'])) {
                $subjects = $subjects->where('grade_class_id', $this->params['grade_class_id']);
            }

            $subjects = $subjects->get();
        }
        if ($this->user && $this->user->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            $educationalSupervisorSubjects = $this->user->educationalSupervisorSubjects;
            if (isset($this->param['grade_class_id'])) {
                $educationalSupervisorSubjects = $educationalSupervisorSubjects->where('grade_class_id', $this->param['grade_class_id']);
            }
            return $this->collection(
                $educationalSupervisorSubjects,
                new \App\OurEdu\LookUp\Transformers\SubjectLookUpTransformer(),
                ResourceTypesEnums::SUBJECT
            );
        }

        return $this->collection(
            $subjects,
            new SubjectLookUpTransformer(),
            ResourceTypesEnums::SUBJECT
        );
    }

    public function includeBranches()
    {
        if ($this->user && $this->user->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            $branch = $this->user->branch;
            if ($branch) {
                return $this->item($branch, new BranchLookupTransformer(), ResourceTypesEnums::SCHOOL_ACCOUNT_BRANCH);
            }
            $branches = $this->user->branches;

            if ($branches->count()) {
                return $this->collection($branches, new BranchLookupTransformer(), ResourceTypesEnums::SCHOOL_ACCOUNT_BRANCH);
            }
        }

        return null;
    }

    public function includeQuizTypes(): Collection
    {
        return $this->collection(QuizTypesEnum::getQuizTypes(), new QuizTypesLookupTransformer(), ResourceTypesEnums::GENERAL_QUIZ);
    }
}
