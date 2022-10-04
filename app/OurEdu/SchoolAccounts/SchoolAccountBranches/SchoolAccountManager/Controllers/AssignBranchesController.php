<?php
namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Controllers;

use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Requests\AssignGradeClassRequest;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Middleware\SchoolAccountBranchMiddleware;

class AssignBranchesController extends BaseController
{
    private $module;
    private $title;
    private $parent;
    private $educationalSystemRepository;
    private $branchRepository;

    public function __construct(
        EducationalSystemRepositoryInterface $educationalSystemRepository,
        SchoolAccountBranchesRepository $branchRepository
    )
    {
        $this->module = 'assign_branch_data';
        $this->title = trans('app.Assign Branches Data');
        $this->branchRepository = $branchRepository;
        $this->educationalSystemRepository = $educationalSystemRepository;
        $this->parent = ParentEnum::SCHOOL_ACCOUNT_MANAGER;
        $this->middleware(SchoolAccountBranchMiddleware::class);

    }
    public function getIndex()
    {
        $user = auth()->user();
        $data['rows'] = $user->schoolAccount->branches()->with('educationalSystems')->paginate(env('PAGE_LIMIT', 20));
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getEdit($id, $educationalSystemId)
    {
        $data['page_title'] = trans('app.Edit').' '.$this->title;
        $data['breadcrumb'] = [$this->title => route('admin.school-account-branches.get.index')];
        $branch = $this->branchRepository->findWith($id,['schoolAccount']);
        $branchEducationalSystem = $this->branchRepository->getBranchEducationalSystem($id, $educationalSystemId);
        $data['branch'] = $branch;
        $data['educationalSystem'] = $this->educationalSystemRepository->find($educationalSystemId);
        $data['gradeClasses'] = $branch->schoolAccount->gradeClasses()->with('translations')->listsTranslations('title')->pluck('title', 'id');
        $data['selectedGradeClasses'] = $branchEducationalSystem->gradeClasses()->pluck('grade_classes.id')->toArray() ?? [];
        $data['academicYears'] = $branch->schoolAccount->academicYears()->with('translations')->listsTranslations('title')->pluck('title', 'id')->toArray();
        $data['educationalTerms'] = $branch->schoolAccount->educationalTerms()->with('translations')->listsTranslations('title')->pluck('title', 'id')->toArray();
        $data['branchEducationalSystem'] = $branchEducationalSystem;
        return view($this->parent.'.'.$this->module.'.edit',$data);
    }


    public function assignGradeClasses(AssignGradeClassRequest $request)
    {
        $branchEducationalSystem = $this->branchRepository->getBranchEducationalSystem($request->branch_id, $request->educational_system_id);

        if ($request->grade_classes)
        $branchEducationalSystem->gradeClasses()->sync($request->grade_classes);

        $updates = [];
        if ($request->academic_year_id)
        $updates += ['academic_year_id' => $request->academic_year_id];
        
        if ($request->educational_term_id)
        $updates += ['educational_term_id' => $request->educational_term_id ];

        $branchEducationalSystem->update($updates);

        flash()->success(trans('app.Update successfully'));
        return redirect()->route('school-account-manager.branch-grade-classes.get.index');
    }


}
