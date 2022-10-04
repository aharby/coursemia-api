<?php
namespace App\OurEdu\SchoolAccounts\GradeClasses\SchoolBranchSupervisor\Controllers;

use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\SchoolAccounts\BranchEducationalSystem;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Middleware\SchoolSupervisorMiddleware;
use App\OurEdu\Users\UserEnums;

class GradeClassSubjectController extends BaseController
{
    private $module;
    private $title;
    private $parent;
    private $subjectRepository;
    /**
     * @var SchoolAccountBranchesRepository
     */
    private $schoolAccountBranchesRepository;

    /**
     * GradeClassSubjectController constructor.
     * @param SubjectRepositoryInterface $subjectRepository
     * @param SchoolAccountBranchesRepository $schoolAccountBranchesRepository
     */
    public function __construct(
        SubjectRepositoryInterface $subjectRepository,SchoolAccountBranchesRepository $schoolAccountBranchesRepository
    )
    {
        $this->module = trans('grade_class');
        $this->title = trans('app.Grade Class Subjects');
        $this->subjectRepository = $subjectRepository;
        $this->parent = ParentEnum::SCHOOL_SUPERVISOR;
        $this->middleware(SchoolSupervisorMiddleware::class);
        $this->schoolAccountBranchesRepository = $schoolAccountBranchesRepository;
    }

    public function getSubjects($gradeClassId, $branchEducationalSystemId, SchoolAccountBranch $branch = null)
    {
        authorize('view-subjectInstructors');

        if (auth()->user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER and $branch and !in_array($branch->id, array_keys($this->schoolAccountBranchesRepository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray()))) {
            abort(403, 'Unauthorized action.');
        }

        $user = auth()->user();
        $data = [];
        $branchEducationalSystem = BranchEducationalSystem::find($branchEducationalSystemId);
        $subjects = $this->subjectRepository->filterSubjectsByBranchEducationalSystemAndGradeClass($branchEducationalSystem,$gradeClassId)->paginate(env('PAGE_LIMIT', 20));

        $data['rows'] = $subjects;
        $data['page_title'] = trans('grade-class.Subjects');
        $data['breadcrumb'] = '';
        $data['parent'] = $this->parent;
        $data['branch'] = $branch;

        return view($this->parent . '.' . $this->module . '.subjects', $data);
    }


}
