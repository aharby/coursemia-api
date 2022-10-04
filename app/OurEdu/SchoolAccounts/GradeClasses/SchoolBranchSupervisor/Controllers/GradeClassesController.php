<?php

namespace App\OurEdu\SchoolAccounts\GradeClasses\SchoolBranchSupervisor\Controllers;

use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GradeClasses\Repository\GradeClassRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Middleware\SchoolSupervisorMiddleware;
use App\OurEdu\Users\UserEnums;

class GradeClassesController extends BaseController
{
    private $module;
    private $title;
    private $parent;
    private $gradeClassRepository;
    /**
     * @var SchoolAccountBranchesRepository
     */
    private $schoolAccountBranchesRepository;

    /**
     * GradeClassesController constructor.
     * @param GradeClassRepositoryInterface $gradeClassRepository
     * @param SchoolAccountBranchesRepository $schoolAccountBranchesRepository
     */
    public function __construct(
        GradeClassRepositoryInterface $gradeClassRepository,
        SchoolAccountBranchesRepository $schoolAccountBranchesRepository
    )
    {
        $this->module = 'grade_class';
        $this->title = trans('app.Grade Classes');
        $this->gradeClassRepository = $gradeClassRepository;
        $this->parent = ParentEnum::SCHOOL_SUPERVISOR;
        $this->user = auth()->user();
        $this->middleware(SchoolSupervisorMiddleware::class);
        $this->schoolAccountBranchesRepository = $schoolAccountBranchesRepository;
    }

    public function getIndex(SchoolAccountBranch $branch = null)
    {
        authorize('view-gradeClasses');

        if (auth()->user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER and $branch and !in_array($branch->id, array_keys($this->schoolAccountBranchesRepository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray()))) {
            abort(403, 'Unauthorized action.');
        }


        $user = auth()->user();
        $data = [];
        $branch = $branch ?? $user->schoolAccountBranchType;
        $gradeClasses = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten();
        $data['rows'] = $gradeClasses;
        $data['page_title'] = $this->title . " " . $branch->name;
        $data['breadcrumb'] = '';
        $data['branch'] = $branch;

        return view($this->parent . '.' . $this->module . '.index', $data);
    }


    public function getEducationalSystems($id, SchoolAccountBranch $branch = null)
    {
        authorize('view-gradeClasses');

        if (auth()->user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER and $branch and !in_array($branch->id, array_keys($this->schoolAccountBranchesRepository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray()))) {
            abort(403, 'Unauthorized action.');
        }

        $branch = $branch ?? auth()->user()->schoolAccountBranchType;
        $data = [];
        $gradeClass = $this->gradeClassRepository->findOrFail($id);
        $branchEducationalSystems = $gradeClass->branchEducationalSystemGradeClass()->with('branchEducationalSystem')
            ->get()->where('branchEducationalSystem.branch_id', $branch->id)
            ->pluck('branchEducationalSystem')->flatten();
        $data['rows'] = $branchEducationalSystems;
        $data['gradeClass'] = $gradeClass;
        $data['page_title'] = trans('app.Grade Classes');
        $data['breadcrumb'] = '';
        $data['branch'] = $branch;

        return view($this->parent . '.' . $this->module . '.educational-systems', $data);
    }

}
