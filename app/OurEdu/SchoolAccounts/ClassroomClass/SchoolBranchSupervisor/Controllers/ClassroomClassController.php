<?php

namespace App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers;

use App\Jobs\CreateClassroomClassSession;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use App\OurEdu\SchoolAccounts\ClassroomClass\ImportJob;
use App\OurEdu\SchoolAccounts\ClassroomClass\Repositories\ClassroomClassRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Enums\ImportJobsStatusEnums;
use App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Requests\ClassroomClassRequest;
use App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Requests\ImportDataRequest;
use App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\UseCases\Imports\ImportJobsUseCaseInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClassroomClassController extends BaseController
{
    private $module;
    private $title;
    private $parent;
    /**
     * @var ClassroomClassRepositoryInterface
     */
    private $classroomClassRepo;
    /**
     * @var ImportJobsUseCaseInterface
     */
    private $importJobsUseCase;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;


    public function __construct(
        ClassroomClassRepositoryInterface $classroomClassRepository,
        ImportJobsUseCaseInterface $importJobsUseCase,
        TokenManagerInterface $tokenManager
    )
    {
        $this->module = 'classroomClasses';
        $this->title = trans('app.Classroom Classes');
//        $this->middleware(SchoolSupervisorMiddleware::class);
        $this->parent = ParentEnum::SCHOOL_SUPERVISOR;
        $this->classroomClassRepo = $classroomClassRepository;
        $this->importJobsUseCase = $importJobsUseCase;
        $this->tokenManager = $tokenManager;
    }

    public function getIndex($classroom)
    {
        authorize('view-classroomClasses');

        $data['rows'] = $this->classroomClassRepo->paginateWhereClassroom($classroom);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('school-branch-supervisor.classrooms.get.index')];
        return view('school_supervisor.classroomClasses.index', $data);
    }

    public function getCreate($classroom)
    {
        authorize('create-classroomClasses');
        $token = $this->tokenManager->createAuthToken(TokenNameEnum::DYNAMIC_lINKS_Token);

        $classroom = Classroom::findOrFail($classroom);
        $branchEducationalSystemGradeClass = $classroom->branchEducationalSystemGradeClass;
        $branchEducationalSystem = $branchEducationalSystemGradeClass->branchEducationalSystem;

        $data['classRoomClass'] = new ClassroomClass();
        $data['subjects'] = Subject::where('educational_system_id', $branchEducationalSystem->educationalSystem->id)
            ->where('grade_class_id', $branchEducationalSystemGradeClass->gradeClass->id)
            ->where('academical_years_id', $branchEducationalSystem->academicYear->id)
            ->where('educational_term_id', $branchEducationalSystem->educationalTerm->id)
            ->pluck('name', 'id');
        $data['parent'] = $this->parent;
        $data['branch'] = $classroom->branch;
        $data['classroom'] = $classroom;
        $data['token'] = $token;

        return view('school_supervisor.classroomClasses.create', $data);
    }

    public function getImport(Classroom $classroom)
    {
        $importedJobs = ImportJob::with("classroom")
            ->where("classroom_id", "=", $classroom->id)
            ->orderByDesc("id")->paginate(env('PAGE_LIMIT', 20));
        $importedJobsStatus = ImportJobsStatusEnums::class;

        return view("school_supervisor.classroomClasses.import", compact("classroom", "importedJobs", "importedJobsStatus"));
    }

    public function showImportJobErrors(ImportJob $job)
    {
        $jobErrors = $job->errors()->orderByDesc("id")->paginate(env('PAGE_LIMIT', 20));

        return view("school_supervisor.classroomClasses.import-errors", compact("jobErrors"));
    }

    public function uploadExcel(ImportDataRequest $request, Classroom $classroom)
    {

        $request->merge(["classroom_id" => $classroom->id]);

        $this->importJobsUseCase->create($request);

        flash()->success(trans('classroomClass.Created Successfully'));
        return redirect()->back();
    }

    public function downloadExcel(ImportJob $job)
    {
        return response()->download(storage_path("/app/public/" . $job->filename));
    }

    public function timetable($classroom)
    {
        authorize('view-classroomClasses');

        $classroom = Classroom::findOrFail($classroom);
        $branchEducationalSystemGradeClass = $classroom->branchEducationalSystemGradeClass;
        $branchEducationalSystem = $branchEducationalSystemGradeClass->branchEducationalSystem;

        $data['classRoomClass'] = new ClassroomClass();
        $data['subjects'] = Subject::where('educational_system_id', $branchEducationalSystem->educationalSystem->id)
            ->where('grade_class_id', $branchEducationalSystemGradeClass->gradeClass->id)
            ->where('academical_years_id', $branchEducationalSystem->academicYear->id)
            ->where('educational_term_id', $branchEducationalSystem->educationalTerm->id)
            ->pluck('name', 'id');
        $data['parent'] = $this->parent;

        return view('school_supervisor.classroomClasses.timetable', $data);
    }

    public function postCreate($classroom, ClassroomClassRequest $request)
    {
        authorize('create-classroomClasses');
        if (!$request->has('tue')  &&
        !$request->has('sun')   &&
        !$request->has('mon')  &&
        !$request->has('wed')  &&
        !$request->has('thu')   &&
        !$request->has('fri')   &&
        !$request->has('sat') &&
        $request->get('repeat') == 3
        ) {
            throw ValidationException::withMessages(
                [
                'contradiction' => trans('classroomClass.please select at least one day')
                ]
            );
        }
        $classroom = Classroom::query()->findOrFail($classroom);

        CreateClassroomClassSession::dispatch($classroom, $request->all())->onQueue("high")->onConnection('redisOneByOne');

        return response()->json(["done and wait queue to process request"]);
    }

    public function getEdit($classroom, $classroomClass)
    {
        authorize('update-classroomClasses');

        $user = auth()->user();
        if ($user->type == UserEnums::SCHOOL_SUPERVISOR) {
            $branch = SchoolAccountBranch::whereNotNull('supervisor_id')->where('supervisor_id', $user->id)->first();
        } else {
            $branch = SchoolAccountBranch::whereNotNull('leader_id')->where('leader_id', $user->id)->first();
        }
        $classroom = Classroom::findOrFail($classroom);
        $branchEducationalSystemGradeClass = $classroom->branchEducationalSystemGradeClass;
        $branchEducationalSystem = $branchEducationalSystemGradeClass->branchEducationalSystem;

        $data['classRoomClass'] = $this->classroomClassRepo->findOrFail($classroomClass);

        $selectedSubject = Subject::find($data['classRoomClass']->subject_id);
        $data['instructors'] = $selectedSubject ? $selectedSubject->schoolInstructors->whereNotNull('branch_id')->where('branch_id', $branch->id)->pluck('name', 'id') : [];
        $data['instructors'] = $selectedSubject ? $selectedSubject->schoolInstructors->pluck('name', 'id') : [];

        $data['subjects'] = Subject::where('educational_system_id', $branchEducationalSystem->educationalSystem->id)
            ->where('grade_class_id', $branchEducationalSystemGradeClass->gradeClass->id)
            ->where('academical_years_id', $branchEducationalSystem->academicYear->id)
            ->where('educational_term_id', $branchEducationalSystem->educationalTerm->id)
            ->pluck('name', 'id');
        $data['parent'] = $this->parent;

        return view('school_supervisor.classroomClasses.edit', $data);
    }

    public function postEdit($classroom, $classroomClass, ClassroomClassRequest $request)
    {
        authorize('update-classroomClasses');

        try {
            $classroomClass = ClassroomClass::findOrFail($classroomClass);
            // Update days
            $request->has('tue') ? $request->merge(['tue' => 1]) : $request->merge(['tue' => 0]);
            $request->has('sun') ? $request->merge(['sun' => 1]) : $request->merge(['sun' => 0]);
            $request->has('mon') ? $request->merge(['mon' => 1]) : $request->merge(['mon' => 0]);
            $request->has('wed') ? $request->merge(['wed' => 1]) : $request->merge(['wed' => 0]);
            $request->has('thu') ? $request->merge(['thu' => 1]) : $request->merge(['thu' => 0]);
            $request->has('fri') ? $request->merge(['fri' => 1]) : $request->merge(['fri' => 0]);
            $request->has('sat') ? $request->merge(['sat' => 1]) : $request->merge(['sat' => 0]);

            $classroomClass->update($request->all());
            $classroomClass->createOrUpdateSessions();
            flash()->success(trans('classroomClass.Updated Successfully'));
            return redirect()->back();
        } catch (ValidationException $exception) {
            throw $exception;
        }
    }

    /*
     * UNUSED!!
     * */
    public function getView($classroom, $classroomClass)
    {
        authorize('view-classroomClasses');

        $classroomClass = ClassroomClass::findOrFail($classroomClass);
        $classroomClass->sessions()->whereDate('from', '>', now())->delete();

        flash()->success(trans('classroomClass.Deleted Successfully'));
        return redirect()->back();
    }

    public function delete($classroom, $classroomClass)
    {
        authorize('delete-classroomClasses');

        $classroomClass = ClassroomClass::findOrFail($classroomClass);

        $checkDelete = $classroomClass->sessions()->where('from', '>=', date('Y-m-d H:i:s'))
            ->where('from', '<=', now()->addMinutes(30)->format('Y-m-d H:i:s'))->first();
        if ($checkDelete) {
            return back()->withErrors(trans('app.can not delete session will start within 30 min'));
        }
        if ($classroomClass->sessions()->where('from', '>=', date('Y-m-d H:i:s'))->exists()) {
            $sessions = $classroomClass->sessions()->where('from', '>=', date('Y-m-d H:i:s'));
            VCRSession::whereIn('classroom_session_id', $sessions->cursor()->pluck('id')->toArray())->delete();
            $sessions->delete();
        }

        if (!$classroomClass->sessions()->where('from', '<=', date('Y-m-d H:i:s'))->exists()) {
            $classroomClass->delete();
        }

        flash()->success(trans('classroomClass.Deleted Successfully'));
        return redirect()->back();
    }
}
