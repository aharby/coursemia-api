<?php

namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Controllers;

use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\UseCases\EducationalSupervisorUseCaseInterface;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Exports\ParentsExport;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Exports\StudentsExport;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Middleware\SchoolSupervisorMiddleware;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Requests\EducationalSupervisorRequest;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\ParentData;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\GradeClasses\GradeClass;
class EducationalSupervisorController extends BaseController
{
    private $module;
    private $title;
    private $parent;
    private $branch;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    private $schoolAccountBranchesRepository;
    private $educationalSupervisorUseCase;
    public function __construct(EducationalSupervisorUseCaseInterface $educationalSupervisorUseCase,UserRepositoryInterface $userRepository,SchoolAccountBranchesRepository $schoolAccountBranchesRepository)
    {
        $this->module = 'educationalSupervisor';
        $this->title = trans('app.Classrooms');
        $this->parent = ParentEnum::SCHOOL_SUPERVISOR;
        $this->middleware(SchoolSupervisorMiddleware::class);
        $this->userRepository = $userRepository;
        $this->schoolAccountBranchesRepository = $schoolAccountBranchesRepository;
        $this->branch = null;
        $this->educationalSupervisorUseCase = $educationalSupervisorUseCase;
    }

    public function getIndex(SchoolAccountBranch $branch = null)
    {
        authorize('view-educationalSupervisor');
        $this->branch = $branch;
        $data['rows'] = $this->educationalSupervisorsLookup()->jsonPaginate(env('PAGE_LIMIT', 20));
        $branch = $branch ?? auth()->user()->schoolAccountBranchType;
        $branchId = $branch->id;
        $data['educational_systems'] = EducationalSystem::whereHas('schoolAccountBranches', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })->with('translations')->listsTranslations('name')->pluck('name', 'id')->toArray();
        $data['grade_classes'] = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten()->pluck('title', 'id');
        $data['page_title'] = trans('app.Data') .' '.trans('app.Educational Supervisor');
        $data['breadcrumb'] = [$this->title => route('school-branch-supervisor.educational-supervisors.get.index')];
        $data['module'] = $this->module;
        $data['branch'] = $this->branch;
        $data['parent'] = $this->parent;

        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function educationalSupervisorsLookup()
    {
        authorize('view-educationalSupervisor');

        $branchId = $this->branch->id ?? auth()->user()->schoolAccountBranchType->id;
        $educationalSupervisors = User::query()
            ->whereHas('branches', function (Builder $builder) use ($branchId) {
                $builder->where("branch_id", "=", $branchId);
            })
            ->where('type',UserEnums::EDUCATIONAL_SUPERVISOR);

        return $educationalSupervisors
            ->when(request('username'), function ($query){
                $query->where('username', request('username'));
            })
            ->when(request('grade_class'), function ($query) {
                $query->whereHas('educationalSupervisorSubjects', function ($query) {
                    $query->where('edu_supervisors_subjects.grade_class_id', request('grade_class'));
                });

            })->when(request('educational_system'), function ($query) {
                $query->whereHas('educationalSupervisorSubjects', function ($query) {
                    $query->where('edu_system_id', request('educational_system'));
                });
        });
    }

    public function getview($educationalSupervisorId){
        authorize('view-educationalSupervisor');

        $data['page_title'] = trans('app.View') .' '.trans('app.Educational Supervisor').' '.trans('app.Data');
        $branch = $branch ?? auth()->user()->schoolAccountBranchType;
        $educationalSupervisor = $this->userRepository->find($educationalSupervisorId);
        $educationalSupervisorSubjects = $educationalSupervisor->educationalSupervisorSubjects ?? collect([]);
        $data['assigned_subjects'] = $educationalSupervisorSubjects;
        $selectedGradeClasses = array_unique($educationalSupervisorSubjects->pluck('pivot.grade_class_id')->toArray());
        $data['gradeClasses'] = $selectedGradeClasses ?
            $branch->schoolAccount->gradeClasses()->whereIn('grade_classes.id',$selectedGradeClasses)
            ->with('translations')->listsTranslations('title')->pluck('title', 'id'):[];
        $data['gradeClasses'] = collect($data['gradeClasses'])->implode(',');
        $data['row'] = $educationalSupervisor;
        $data['parent'] = $this->parent;

        return view($this->parent . '.' . $this->module . '.view', $data);
    }


    public function edit(User $educationalSupervisor, SchoolAccountBranch $branch = null)
    {
        authorize('update-educationalSupervisor');

        if (Auth::user()->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            $branches = \auth()->user()->branches()->pluck('id')->toArray();
            $educationalSystems = $this->schoolAccountBranchesRepository->getEducationalSystemsByBranches($branches)->pluck('name', 'id');
        } else {
            $branch = $branch ?? auth()->user()->schoolAccountBranchType;
            $branchId = $branch->id;
            $educationalSystems = $this->schoolAccountBranchesRepository->getEducationalSystemsByBranch($branchId)->pluck('name', 'id');
        }

        $educationalSupervisorSubjects = $educationalSupervisor->educationalSupervisorSubjects ?? collect([]);

        $selectedGradeClasses = array_unique($educationalSupervisorSubjects->pluck('pivot.grade_class_id')->toArray());

        $selectedEducationalSystem = $educationalSupervisorSubjects->first()?$educationalSupervisorSubjects->first()->pivot->edu_system_id:null;

        $selectedSubjects = $educationalSupervisorSubjects->pluck('id')->toArray();

        $gradeClasses = $selectedEducationalSystem ?
            $branch->schoolAccount->gradeClasses()->where('educational_system_id',$selectedEducationalSystem)
            ->with('translations')->listsTranslations('title')->pluck('title', 'id'):[];

        $subjects = $selectedGradeClasses?$this->schoolAccountBranchesRepository->getSubjectsByGradeClass($selectedGradeClasses):null;

        $page_title = trans('app.edit'). ' '.trans('app.Data') .' '.trans('app.Educational Supervisor');

        $data = compact(
            "selectedGradeClasses","selectedEducationalSystem",'selectedSubjects','subjects',
            "educationalSupervisor", "page_title", "branch","educationalSystems","gradeClasses"
        );

        return view($this->parent . '.' . $this->module . '.edit', $data);
    }

    public function update(EducationalSupervisorRequest $request, User $educationalSupervisor, SchoolAccountBranch $branch = null)
    {
        authorize('update-educationalSupervisor');
        $this->educationalSupervisorUseCase->updateEducationalSupervisor($request,$educationalSupervisor);
        flash()->success(trans('app.Update successfully'));
        return redirect()->route('school-branch-supervisor.educational-supervisors.get.index',["branch" => $branch]);
    }
}
