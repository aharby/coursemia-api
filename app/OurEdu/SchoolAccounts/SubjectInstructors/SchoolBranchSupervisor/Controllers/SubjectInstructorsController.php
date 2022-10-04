<?php

namespace App\OurEdu\SchoolAccounts\SubjectInstructors\SchoolBranchSupervisor\Controllers;

use App\OurEdu\SchoolAccounts\SubjectInstructors\SchoolBranchSupervisor\Exports\SchoolInstructorsExport;
use App\OurEdu\SchoolAccounts\SubjectInstructors\SchoolBranchSupervisor\Requests\EditSchoolInstructorRequest;
use Carbon\Carbon;
use Excel;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Users\{Repository\UserRepositoryInterface,
    UseCases\CreateZoomUserUserCase\CreateZoomUserUseCaseInterface,
    UseCases\ResetSchoolInstructorPasswordUseCase\ResetSchoolInstructorPasswordUseCaseInterface,
    UseCases\UpdateUserUseCase\UpdateUserUseCase,
    User,
    UserEnums};
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\SchoolAccounts\Classroom;
use Symfony\Component\HttpFoundation\Request;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Middleware\SchoolSupervisorMiddleware;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\SchoolAccounts\SubjectInstructors\SchoolBranchSupervisor\Imports\InstructorsImport;
use App\OurEdu\SchoolAccounts\SubjectInstructors\SchoolBranchSupervisor\Requests\UpdateSchoolInstructorPasswordRequest;

class SubjectInstructorsController extends BaseController
{
    private $title;
    private $module;
    private $parent;
    private $repository;
    private $userRepository;
    private $resetSchoolInstructorPasswordUseCase;
    private $createZoomUser;

    public function __construct(
        SchoolAccountBranchesRepository $schoolAccountBranchesRepository,
        ResetSchoolInstructorPasswordUseCaseInterface $resetSchoolInstructorPasswordUseCase,
        UserRepositoryInterface $userRepository,
        CreateZoomUserUseCaseInterface  $createZoomUser
    )
    {
        $this->module = trans('grade_class');
        $this->title = trans('app.Grade Classes');
        $this->repository = $schoolAccountBranchesRepository;
        $this->parent = ParentEnum::SCHOOL_SUPERVISOR;
        $this->middleware(SchoolSupervisorMiddleware::class);
        $this->resetSchoolInstructorPasswordUseCase = $resetSchoolInstructorPasswordUseCase;
        $this->userRepository = $userRepository;
        $this->createZoomUser = $createZoomUser;


    }

    public function getSchoolInstructors(SchoolAccountBranch $branch = null)
    {
        authorize('view-subjectInstructors');

        if ($branch and !in_array($branch->id, array_keys($this->repository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray())) and Auth::user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER) {
            abort(403, 'Unauthorized action.');
        }

        $branch = $branch ?? auth()->user()->schoolAccountBranchType;

        $instructors = User::query()->where('type', UserEnums::SCHOOL_INSTRUCTOR)->where('branch_id', $branch->id);

        if (request()->has("deactivated")) {
            $instructors->where("is_active", "=", 0);
        }

        $data['rows'] = $instructors->paginate(env('PAGE_LIMIT', 20));

        $data['page_title'] = trans('schools.Instructors');
        $data['parent'] = $this->parent;
        $data['branch'] = $branch;


        return view($this->parent . '.' . $this->module . '.instructors', $data);

    }

    public function getView(SchoolAccountBranch $branch = null)
    {
        authorize('view-subjectInstructors');
        if ($branch and !in_array($branch->id, array_keys($this->repository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray())) and Auth::user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER) {
            abort(403, 'Unauthorized action.');
        }

        $branch = $branch ?? auth()->user()->schoolAccountBranchType;

        $instructors = User::with([ 'schoolInstructorSubjects.educationalSystem', 'schoolInstructorSubjects.gradeClass', 'schoolInstructorSubjects.academicalYears'])
            ->where('type', UserEnums::SCHOOL_INSTRUCTOR)->where('branch_id', $branch->id)->get();

        $data['rows'] = $instructors;

        $data['page_title'] = trans('schools.Instructors');
        $data['parent'] = $this->parent;
        $data['branch'] = $branch;


        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function exportInstructors(SchoolAccountBranch $branch = null)
    {
        if ($branch and !in_array($branch->id, array_keys($this->repository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray())) and Auth::user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER) {
            abort(403, 'Unauthorized action.');
        }

        $branch = $branch ?? auth()->user()->schoolAccountBranchType;

        $instructors = User::query()
            ->where('type', UserEnums::SCHOOL_INSTRUCTOR)
            ->where('branch_id', $branch->id)
            ->with('schoolInstructorSubjects')
            ->get();

        return Excel::download(new SchoolInstructorsExport($instructors), "school instructors data.xlsx");
    }

    public function getUpdateInstructor($instructorUserId, SchoolAccountBranch $branch = null)
    {
        // subjectInstructors = schoolInstructors
        authorize('update-subjectInstructors');

        if ($branch and !in_array($branch->id, array_keys($this->repository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray())) and Auth::user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER) {
            abort(403, 'Unauthorized action.');
        }

        $branch = $branch ?? auth()->user()->schoolAccountBranchType;


        $data['row'] = $this->userRepository->findOrFail($instructorUserId);
        $data['page_title'] = trans('app.edit') . ' '.  trans('app.Data') .' '. trans('instructors.instructor');
        $data['parent'] = $this->parent;
        $data['branch'] = $branch;
        return view($this->parent . '.' . $this->module . '.edit', $data);
    }

    public function putUpdateInstructor(EditSchoolInstructorRequest $request, UpdateUserUseCase $updateUserUseCase, $instructorUserId, SchoolAccountBranch $branch = null)
    {
        // subjectInstructors = schoolInstructors
        authorize('update-subjectInstructors');

        if ($branch and !in_array($branch->id, array_keys($this->repository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray())) and Auth::user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER) {
            abort(403, 'Unauthorized action.');
        }
        $branch = $branch ?? auth()->user()->schoolAccountBranchType;

        if ($updateUserUseCase->updateUser($this->userRepository, $request->all(), $instructorUserId)) {
            flash(trans('app.Update successfully'))->success();
            return redirect()->route('school-branch-supervisor.subject-instructors.get.school-instructor', ["branch" => $branch]);
        }
        flash()->error(trans('app.failed to save'));
        return  redirect()->back();
    }

    public function getClassrooms()
    {
        authorize('view-classrooms');

        $data['rows'] = Classroom::all();
        // todo : add filter classrooms
        $data['page_title'] = trans('app.View') . ' ' . trans('app.Classrooms');
        $data['breadcrumb'] = [$this->title => route('admin.countries.get.index')];
        $data['parent'] = $this->parent;
        return view($this->parent . '.' . $this->module . '.grade-classes', $data);

    }


    public function importSubjectInstructors(Request $request, $subjectId, SchoolAccountBranch $branch = null)
    {
        authorize('create-subjectInstructors');

        if (auth()->user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER and $branch and !in_array($branch->id, array_keys($this->repository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray()))) {
            abort(403, 'Unauthorized action.');
        }

        $branch = $branch ?? auth()->user()->schoolAccountBranchType;
        $branchId = $branch->id;
        $data = [
            'branch_id' => $branchId,
            'subject_id' => $subjectId,
        ];
        if ($request->file('file')) {
            Excel::import(new InstructorsImport($data, $this->resetSchoolInstructorPasswordUseCase, $this->userRepository , $this->createZoomUser), $request->file('file'));
        }

        flash()->success(trans('app.Instructors added successfully'));
        return redirect()->back();

    }

    public function activeInstructor(User $user){
        authorize('update-subjectInstructors');


        $classRoomSessions = $user->schoolInstructorSessions()
            ->where("to", ">", Carbon::now())
            ->first();

        if ($classRoomSessions && $user->is_active == 1) {
            return redirect()->back()->with(['error' => trans('app.You should delete his sessions first')]);
        }

        if ($user->is_active == 0) {
            $user->update(['is_active' => 1]);
            return redirect()->back()->with(['success' => trans('app.Instructors Activated successfully')]);
        }
        $user->update(['is_active' => 0]);
        return redirect()->back()->with(['success' => trans('app.Instructors Deactivated successfully')]);
    }

}
