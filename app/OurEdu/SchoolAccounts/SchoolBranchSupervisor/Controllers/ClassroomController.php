<?php

namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\SchoolAccounts\BranchEducationalSystem;
use App\OurEdu\SchoolAccounts\BranchEducationalSystemGradeClass;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Jobs\DeleteClassStudentsAndTheirParents;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Middleware\SchoolSupervisorMiddleware;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Requests\ClassroomRequest;
use App\OurEdu\Subjects\Admin\Imports\StudentsImport;
use App\OurEdu\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCase;
use App\OurEdu\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCaseInterface;
use App\OurEdu\Users\UserEnums;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\SchoolAdmin\FormativeTest\Jobs\SaveFormativeTestClassroomJob;
use Maatwebsite\Excel\Facades\Excel;

class ClassroomController extends BaseController
{
    private $module;
    private $title;
    private $parent;
    /**
     * @var SchoolAccountBranchesRepository
     */
    private $schoolAccountBranchesRepository;
    private $generalQuizRepo;

    public function __construct(
        SchoolAccountBranchesRepository $schoolAccountBranchesRepository,
        GeneralQuizRepositoryInterface $generalQuizRepo
        )
    {
        $this->module = 'classrooms';
        $this->title = trans('app.Classrooms');
        $this->parent = ParentEnum::SCHOOL_SUPERVISOR;
        $this->middleware(SchoolSupervisorMiddleware::class);
        $this->schoolAccountBranchesRepository = $schoolAccountBranchesRepository;
        $this->generalQuizRepo = $generalQuizRepo;
    }

    public function getIndex(SchoolAccountBranch $branch = null)
    {
        authorize('view-classrooms');
        if (auth()->user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER and $branch and !in_array($branch->id, array_keys($this->schoolAccountBranchesRepository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray()))) {
            abort(403, 'Unauthorized action.');
        }
        $branch = $branch ?? auth()->user()->schoolAccountBranchType;
        $data['branch'] = $branch;
        $data['rows'] = $branch->classrooms()->where('is_special',0)->with('branchEducationalSystemGradeClass')->paginate(env('PAGE_LIMIT', 20));
        $data['page_title'] = trans('app.List') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('school-branch-supervisor.classrooms.get.index')];
        $data['parent'] = $this->parent;
        $data['branch'] = $branch;

        return view($this->parent . '.' . $this->module . '.index', $data);

    }

    public function getTrashedClassrooms(): View
    {
        $branch = auth()->user()->schoolAccountBranchType;

        $classrooms = $branch->classrooms()
            ->onlyTrashed()
            ->with('branch')
            ->paginate();

        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['rows'] = $classrooms;
        return view($this->parent . '.' . $this->module . '.trashed', $data);
    }

    public function getCreate(SchoolAccountBranch $branch = null)
    {
        authorize('create-classrooms');
        if (auth()->user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER and $branch and !in_array($branch->id, array_keys($this->schoolAccountBranchesRepository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray()))) {
            abort(403, 'Unauthorized action.');
        }

        $data['page_title'] = trans('app.Create') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('school-branch-supervisor.classrooms.get.create')];
        $branch = $branch ?? auth()->user()->schoolAccountBranchType;

        $data['gradeClasses'] = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten()->pluck('title', 'id');
        $data['educationalSystems'] = $branch->educationalSystems->pluck('name', 'id');
        $data['academicYears'] = $branch->branchEducationalSystem()->with('academicYear')->get()->pluck('academicYear')->flatten()->pluck('title', 'id');
        $data['educationalTerms'] = $branch->branchEducationalSystem()->with('educationalTerm')->get()->pluck('educationalTerm')->flatten()->pluck('title', 'id');
        $data['parent'] = $this->parent ;
        $data['branch'] = $branch;

        return view($this->parent . '.' . $this->module . '.create', $data);

    }

    public function postCreate(ClassroomRequest $request, SchoolAccountBranch $branch = null)
    {
        authorize('create-classrooms');
        if (auth()->user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER and $branch and !in_array($branch->id, array_keys($this->schoolAccountBranchesRepository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray()))) {
            abort(403, 'Unauthorized action.');
        }
        $branch = $branch ?? auth()->user()->schoolAccountBranchType;
        $branchId = $branch->id;
        $branchEducationalSystemId = BranchEducationalSystem::where('branch_id', $branchId)
            ->where('educational_system_id', $request->educational_system_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('educational_term_id', $request->educational_term_id)
            ->first()->id;

        $branchEduSysGradeClassId = BranchEducationalSystemGradeClass::where('grade_class_id', $request->grade_class_id)
            ->where('branch_educational_system_id', $branchEducationalSystemId)->first()->id;
        return DB::transaction(function () use($request,$branchId,$branchEduSysGradeClassId,$branch) {
            $newClassRoom = Classroom::create(
                [
                   'name' => $request->name,
                   'branch_id' => $branchId,
                   'branch_edu_sys_grade_class_id' => $branchEduSysGradeClassId,
                ]
            );

            if ($request->file('file')) {
                $data = [
                    'classroom_id' => $newClassRoom->id,
                ];
                Excel::import(new StudentsImport($data, new CreateZoomUserUseCase()), $request->file('file'));
            }

            $gradeClass= GradeClass::query()
                ->where('id', $request->grade_class_id)
                ->where('educational_system_id', $request->educational_system_id)
                ->first();

            if (isset($gradeClass) && $newClassRoom) {
                SaveFormativeTestClassroomJob::dispatch($gradeClass, $newClassRoom);
            }

            if ($newClassRoom) {
                flash()->success(trans('app.Created successfully'));
                return redirect()->route('school-branch-supervisor.classrooms.get.index', ["branch" => $branch]);
            } else {
                flash()->error(trans('app.Oopps Something is broken'));
                return redirect()->back();
            }
        }
        );
    }

    public function getEdit($id, SchoolAccountBranch $branch = null)
    {
        authorize('update-classrooms');
        if (auth()->user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER and $branch and !in_array($branch->id, array_keys($this->schoolAccountBranchesRepository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray()))) {
            abort(403, 'Unauthorized action.');
        }

        $data['row'] = Classroom::find($id);

        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;

        $branchEducationalSystemGradeClass = $data['row']->branchEducationalSystemGradeClass;
        $branchEducationalSystem = $branchEducationalSystemGradeClass->branchEducationalSystem;
        $data['row']['gradeClass'] = $branchEducationalSystemGradeClass->gradeClass->id;
        $data['row']['educationalSystem'] = $branchEducationalSystem->educational_system_id;
        $data['row']['academicYear'] = $branchEducationalSystem->academic_year_id;
        $data['row']['educationalTerm'] = $branchEducationalSystem->educational_term_id;

        $branch = $branch ?? auth()->user()->schoolAccountBranchType;
        $data['gradeClasses'] = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten()->pluck('title', 'id');
        $data['educationalSystems'] = $branch->educationalSystems->pluck('name', 'id');
        $data['academicYears'] = $branch->branchEducationalSystem()->with('academicYear')->get()->pluck('academicYear')->flatten()->pluck('title', 'id');
        $data['educationalTerms'] = $branch->branchEducationalSystem()->with('educationalTerm')->get()->pluck('educationalTerm')->flatten()->pluck('title', 'id');
        $data['parent'] = $this->parent ;
        return view($this->parent . '.' . $this->module . '.edit', $data);
    }


    public function putEdit(ClassroomRequest $request, $id, SchoolAccountBranch $branch = null)
    {
        authorize('update-classrooms');
        if (auth()->user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER and $branch and !in_array($branch->id, array_keys($this->schoolAccountBranchesRepository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray()))) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->all();
        $classroom = Classroom::find($id);


        $classroom->update([
            'name' => $request->name,
        ]);

        if ($request->file('file')) {
            $data = [
                'classroom_id' => $classroom->id,
            ];
            Excel::import(new StudentsImport($data,new CreateZoomUserUseCase()), $request->file('file'));
        }

        flash()->success(trans('app.Update successfully'));
        return redirect()->route('school-branch-supervisor.classrooms.get.index', ["branch" => $branch]);
    }

    public function getView($id, SchoolAccountBranch $branch)
    {
        authorize('view-classrooms');
        if (auth()->user()->type == UserEnums::SCHOOL_ACCOUNT_MANAGER and $branch and !in_array($branch->id, array_keys($this->schoolAccountBranchesRepository->getBranchesBySchoolAccountManagerPluck(auth()->id())->toArray()))) {
            abort(403, 'Unauthorized action.');
        }

        $data['row'] = Classroom::find($id);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('school-branch-supervisor.classrooms.get.index')];
        $data['parent'] = $this->parent;
        $data['branch'] = $branch;

        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function delete($id)
    {
        authorize('delete-classrooms');
        try {
            $classroom = Classroom::findOrFail($id);
            $this->deleteClassroomData($classroom);
            return redirect()
                    ->route('school-branch-supervisor.classrooms.get.index')
                    ->with(['success'=>trans('app.Deleted Successfully')]);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }
    }

    private function deleteClassroomData($classroom)
    {
        // checking if there is sessions in the next 30 mins
        foreach ($classroom->classroomClassSessions as $classroomClassSession) {
            if ((new Carbon($classroomClassSession->from))->isBetween(now(), now()->addMinutes(30))) {
                throw ValidationException::withMessages([
                    'error' => trans('app.can not delete classroom that has session will start within 30 min')
                ]);
            }
        }
        $classroom->classroomClassSessions()->delete();
        $classroom->vcrSessions()->delete();
        $classroom->classroomClass()->delete();

        // deleting the students users and parents
        DeleteClassStudentsAndTheirParents::dispatch($classroom);
    }
}
