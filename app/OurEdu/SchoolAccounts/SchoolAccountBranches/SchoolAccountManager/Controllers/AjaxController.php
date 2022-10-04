<?php
namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClass\Repositories\ClassroomClassRepositoryInterface;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Student\Transformers\ClassroomClassSessionsTransformer;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use http\Env\Response;
use Illuminate\Database\Eloquent\Builder;

class AjaxController extends BaseApiController
{
    /**
     * @var ClassroomClassRepositoryInterface
     */
    private $classroomClassRepository;
    /**
     * @var ClassroomRepositoryInterface
     */
    private $classroomRepository;

    /**
     * AjaxController constructor.
     * @param ClassroomClassRepositoryInterface $classroomClassRepository
     * @param ClassroomRepositoryInterface $classroomRepository
     */
    public function __construct(ClassroomClassRepositoryInterface $classroomClassRepository, ClassroomRepositoryInterface $classroomRepository)
    {
        $this->classroomClassRepository = $classroomClassRepository;
        $this->classroomRepository = $classroomRepository;
    }

    public function getClassroomSubjects()
    {

        $this->validate(request(),[
           'classroom_id' => 'required',
        ]);

        $classroom = $this->classroomRepository->find(request()->get('classroom_id'));
        $subject_ids = array_unique($this->classroomClassRepository->getByClassroom($classroom)->pluck('subject_id')->toArray());

        $subjects = Subject::query();
        $subjectsWithGradeClassName = $subjects
            ->join('grade_class_translations as g', 'g.grade_class_id', '=', 'subjects.grade_class_id')
            ->whereIn('subjects.id',$subject_ids)
            ->where('g.locale',app()->getLocale())
            ->selectRaw('CONCAT(subjects.name, " - ", g.title) as fullNameSubj, subjects.id')
            ->pluck("fullNameSubj", 'id');

        return response()->json([
            "status" => 200,
            "subjects" => $subjectsWithGradeClassName,
        ]);
    }

    public function getBranchSubject(SchoolAccountBranch $branch)
    {
        $branchEducationalSystemsIDs = $branch->branchEducationalSystem()->pluck("educational_system_id")->toArray();
        $branchEducationalSystemsAcademicYears = $branch->branchEducationalSystem()->pluck("academic_year_id")->toArray();
        $branchEducationalSystemsEdcayionalTerms = $branch->branchEducationalSystem()->pluck("educational_term_id")->toArray();

        $educationalSystem = EducationalSystem::query()->whereIn("id", $branchEducationalSystemsIDs)->pluck("id")->toArray();

        $subjects = Subject::query()
            ->whereIn('educational_system_id', $educationalSystem)
            ->whereIn('academical_years_id', $branchEducationalSystemsAcademicYears)
            ->whereIn('educational_term_id', $branchEducationalSystemsEdcayionalTerms)
            ->pluck("name", "id");

        return response()->json([
            "status" => 200,
            "subjects" => $subjects,
        ]);
    }

    public function getGradeSubjects(GradeClass $gradeClass, SchoolAccountBranch $branch = null)
    {
        $branch = $branch ?? Auth::user()->branch;
        $branchEducationalSystemsIDs = $branch->branchEducationalSystem()->pluck("educational_system_id")->toArray();
        $branchEducationalSystemsAcademicYears = $branch->branchEducationalSystem()->pluck("academic_year_id")->toArray();
        $branchEducationalSystemsEdcayionalTerms = $branch->branchEducationalSystem()->pluck("educational_term_id")->toArray();

        $educationalSystem = EducationalSystem::query()->whereIn("id", $branchEducationalSystemsIDs)->pluck("id")->toArray();

        $subjects = Subject::query()
            ->whereIn('educational_system_id', $educationalSystem)
            ->whereIn('academical_years_id', $branchEducationalSystemsAcademicYears)
            ->whereIn('educational_term_id', $branchEducationalSystemsEdcayionalTerms)
            ->where("grade_class_id", "=", $gradeClass->id)
            ->pluck("name", "id");

        return response()->json([
            "status" => 200,
            "subjects" => $subjects,
        ]);
    }

    public function getBranchQuizCreator(SchoolAccountBranch $branch)
    {
        $instructors = User::query()
            ->whereHas("quizzes" , function (Builder $quiz) use ($branch) {
                $quiz->where("branch_id", "=", $branch->id);
            })
            ->get();

        return response()->json([
            "status" => 200,
            "instructors" => $instructors,
        ]);
    }
}
