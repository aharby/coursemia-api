<?php

namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Exports\ParentsExport;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Exports\StudentsExport;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Middleware\SchoolSupervisorMiddleware;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Requests\StudentParentRequest;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Requests\StudentRequest;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\ParentData;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class SchoolStudentsController extends BaseController
{
    private $module;
    private $title;
    private $parent;
    private $branch;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->module = 'students';
        $this->title = trans('app.Classrooms');
        $this->parent = ParentEnum::SCHOOL_SUPERVISOR;
        $this->middleware(SchoolSupervisorMiddleware::class);
        $this->userRepository = $userRepository;
        $this->branch = null;
    }

    public function getIndex(SchoolAccountBranch $branch = null)
    {
        authorize('view-students');
        $this->branch = $branch;
        $data['rows'] = $this->studentsLookup()->jsonPaginate(env('PAGE_LIMIT', 20));
        $branch = $branch ?? auth()->user()->schoolAccountBranchType;
        $branchId = $branch->id;
        $data['classrooms'] = Classroom::where('branch_id', $branchId)->where('is_special',0)->pluck('name', 'id')->toArray();
        $data['educational_systems'] = EducationalSystem::whereHas('schoolAccountBranches', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })->with('translations')->listsTranslations('name')->pluck('name', 'id')->toArray();
        $data['grade_classes'] = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten()->pluck('title', 'id');
        $data['page_title'] = request()->has('deactivated') ?  trans('app.List') . ' '. trans('quiz.students')  : trans('app.List Students');
        $data['breadcrumb'] = [$this->title => route('school-branch-supervisor.students.get.index')];
        $data['module'] = $this->module;
        $data['parent'] = $this->parent;
        $data['branch'] = $this->branch;
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function studentsLookup($parent = false)
    {
        authorize('view-students');

        $branchId = $this->branch->id ?? auth()->user()->schoolAccountBranchType->id;

        $classroomIds = Classroom::where('branch_id', $branchId)->pluck('id');
        $students = Student::whereIn('classroom_id', $classroomIds)->with('user.parents.parentData')->whereHas('user');
        $whereHasRelationString = 'user';
        if($parent){
            $students = $students->whereHas('user.parents');
            $whereHasRelationString = 'user.parents';
        }
        return $students

            ->when(request('mobile'), function ($query) {
                $query->whereHas('user.parents',function($query){
                    $query->where('mobile','LIKE','%' .request('mobile'). '%' );
                });
            })
            ->when(request('username'), function ($query) use($whereHasRelationString){
                $query->whereHas($whereHasRelationString, function ($query) {
                    $query->where('username', request('username'));
                });
            })->when(request('classroom'), function ($query) {
                $query->where('classroom_id', request('classroom'));
            })->when(request('grade_class'), function ($query) {
                $query->where('class_id', request('grade_class'));
            })->when(request('educational_system'), function ($query) {
                $query->where('educational_system_id', request('educational_system'));
            })
            ->when(request()->has("deactivated"), function ($query) {
                $query->whereHas("user", function ($user) {
                    $user->where("is_active", "=", 0);
                });
            });
    }

    public function getParents(SchoolAccountBranch $branch = null)
    {
        authorize('view-parents');

        $this->branch = $branch;
        $branch = $branch ?? auth()->user()->schoolAccountBranchType;
        $branchId = $branch->id;
        $data['classrooms'] = Classroom::where('branch_id', $branchId)->pluck('name', 'id')->toArray();
        $data['educational_systems'] = EducationalSystem::whereHas('schoolAccountBranches', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })->with('translations')->listsTranslations('name')->pluck('name', 'id')->toArray();
        $data['grade_classes'] = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten()->pluck('title', 'id');
        $data['rows'] = $this->studentsLookup(true)->jsonPaginate(env('PAGE_LIMIT',20));
        $data['page_title'] = trans('app.List') . ' ' .trans('parents.parents');
        $data['breadcrumb'] = [$this->title => route('school-branch-supervisor.students.get.parents')];
        $data['parent'] = $this->parent ;
        $data['branch'] = $this->branch ;

        return view($this->parent . '.' . $this->module . '.parents', $data);

    }

    public function exportStudents(SchoolAccountBranch $branch =null)
    {
        $this->branch = $branch;
        $data['rows'] = $this->studentsLookup()->get();

        return Excel::download(new StudentsExport($data['rows'], $this->exportHeadings()), $this->module . '-passwords.xls');
    }

    private function exportHeadings()
    {
        authorize('view-students');

        return [
            trans('students.name'),
            trans('students.username'),
            trans('students.password'),
            trans('students.classroom'),
            trans('students.grade class'),
            trans('students.educational system'),
        ];
    }

    public function exportParents(SchoolAccountBranch $branch =null)
    {
        authorize('view-students');
        $this->branch = $branch;

        $data['rows'] = $this->studentsLookup(true)->get();
        $data['branch'] = $this->branch;
        return Excel::download(new ParentsExport($data['rows']), $this->module . 'parents-passwords.xls');
    }

    private function exportParentHeadings()
    {
        authorize('view-students');

        return [
            trans('parents.name'),
            trans('parents.email'),
            trans('parents.password'),
        ];
    }
    public function getviewStudent($studentId){
        authorize('view-students');

        $data['page_title'] = trans('app.Data') .' '.trans('app.Student');

        $student = Student::find($studentId);
        $data['row'] = $student;
        $data['parentsStudent'] = $student->user->parents ;
        $data['parent'] = $this->parent;

        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function activeStudent($studentId){
        authorize('update-students');

        $student = Student::find($studentId);

        if($student->user->is_active == 0){
            $student->user->update(['is_active' => 1]);
            foreach ($student->user->parents as $parent){
                $parent->update(['is_active' => 1]);
            }
            return redirect()->back()->with(['success' => trans('app.Student Activated successfully')]);
        }
        $student->user->update(['is_active' => 0]);
        foreach ($student->user->parents as $parent){
            $parent->update(['is_active' => 0]);
        }
        return redirect()->back()->with(['success' => trans('app.Student Deactivated successfully')]);
    }

    public function edit(Student $student, SchoolAccountBranch $branch = null)
    {
        $student->user = $student->user;
        $page_title = trans('app.Edit Student');
        $classrooms = Classroom::query()
            ->whereHas("branch", function (Builder $query) use ($student) {
                $query->where("id", "=", $student->user->branch_id);
            })->where('is_special',0)->pluck("name", "id");


        $data = compact("student", "page_title", "classrooms", "branch");

        return view($this->parent . '.' . $this->module . '.edit', $data);
    }

    public function update(StudentRequest $request, Student $student, SchoolAccountBranch $branch = null)
    {
        $userData = [
            "first_name" => $request->get("first_name"),
            "last_name" => $request->get("last_name"),
            "username" => $request->get("username"),
            "mobile" => $request->get("mobile"),
            "email" => $request->get("email"),
        ];

        if ($request->filled("password")) {
            $userData["password"] =$request->get("password");

            $student->password =  $request->get("password");
            $student->save();
        }

        $student->user->update($userData);

        if ($request->get("classroom_id") != $student->classroom_id) {
            $newClassroom = Classroom::query()
                ->with("branchEducationalSystemGradeClass.branchEducationalSystem")
                ->where("id", "=", $request->get("classroom_id"))
                ->first();

            $branchEducationalSystemGradeClass = $newClassroom->branchEducationalSystemGradeClass;
            $branchEducationalSystem = $branchEducationalSystemGradeClass->branchEducationalSystem;
            $gradeClassId = $branchEducationalSystemGradeClass->gradeClass;
            $countryId = $branchEducationalSystem->branch->schoolAccount->country_id;

            $studentData = [
                "classroom_id" => $newClassroom->id,
                "educational_system_id" => $branchEducationalSystem->educational_system_id,
                "class_id" => $gradeClassId->id,
                "academic_year_id" => $branchEducationalSystem->academic_year_id,
            ];

            $student->update($studentData);

            // subscribe student to all classroom subjects
            $subjects = Subject::where('educational_system_id', $branchEducationalSystem->educational_system_id)
                ->where('country_id', $countryId)
                ->where('grade_class_id', $gradeClassId->id)
                ->where('educational_term_id', $branchEducationalSystem->educational_term_id)
                ->where('academical_years_id', $branchEducationalSystem->academic_year_id)
                ->get();

            $student->subjects()->sync($subjects->pluck('id')->toArray());
        }


        flash()->success(trans('app.Update successfully'));
        return redirect()->route('school-branch-supervisor.students.get.index',["branch" => $branch]);
    }

    public function parentCreate(Student $student)
    {
        $page_title = trans("students.Add Parent");

        return view($this->parent . '.' . $this->module . '.parents.create', compact("student", "page_title"));
    }

    public function parentStore(StudentParentRequest $request, Student $student)
    {
        authorize("create-parents");
        $parent = User::query()->where("username", "=", $request->get("username"))->first();

        if (isset($parent)) {
            $student->user->parents()->syncWithoutDetaching($parent->id);
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('school-branch-supervisor.students.get.view-student', $student);
        }

        $data = [
            "first_name" => $request->get("first_name"),
            "last_name" => $request->get("last_name"),
            "username" => $request->get("username"),
            "email" => $request->get("email"),
            "mobile" => $request->get("mobile"),
            "password" =>$request->get("username"), //This password is hashed but in user model has setPasswordAttribute($val)
            "type" => UserEnums::PARENT_TYPE,
            'language' => "ar",
            'confirmed'=>1
        ];

        $parent = $this->userRepository->create($data);

        $student->user->parents()->syncWithoutDetaching($parent->id);

        flash()->success(trans('app.Created successfully'));
        return redirect()->route('school-branch-supervisor.students.get.view-student', $student);
    }

    public function parentEdit(Student $student, User $parent)
    {
        $page_title = trans("students.Edit Student Parent");

        return view($this->parent . '.' . $this->module . '.parents.edit', compact("student", "parent", "page_title"));
    }

    public function parentUpdate(StudentParentRequest $request, Student $student, User $parent)
    {
        $data = [
            "first_name" => $request->get("first_name"),
            "last_name" => $request->get("last_name"),
            "username" => $request->get("username"),
            "email" => $request->get("email"),
            "mobile" => $request->get("mobile"),
        ];

        $this->userRepository->update($parent, $data);

        flash()->success(trans('app.Updated successfully'));
        return redirect()->route('school-branch-supervisor.students.get.view-student', $student);
    }

    public function parentDelete(Student $student, User $parent)
    {
        $student->user->parents()->detach($parent->id);

        flash()->success(trans('app.Delete successfully'));
        return redirect()->route('school-branch-supervisor.students.get.view-student', [$student->id]);
    }

}
