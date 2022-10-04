<?php


namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\SchoolAccounts\BranchEducationalSystem;
use App\OurEdu\SchoolAccounts\BranchEducationalSystemGradeClass;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Middleware\SchoolSupervisorMiddleware;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Requests\SpecialClassRoomRequest;
use App\OurEdu\Users\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpecialClassroomController extends  BaseController
{

    private $module;
    private $title;
    private $parent;

    public function __construct()
    {
        $this->module = 'specialClassrooms';
        $this->title = trans('app.SpecialClassrooms');
        $this->parent = ParentEnum::SCHOOL_SUPERVISOR;
        $this->middleware(SchoolSupervisorMiddleware::class);
    }

    public function getIndex()
    {
        authorize('view-classrooms');

        $branch = auth()->user()->schoolAccountBranchType;
        $data['rows'] = $branch->classrooms()->where('is_special',1)->with('branchEducationalSystemGradeClass')->paginate(env('PAGE_LIMIT', 20));
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('school-branch-supervisor.classrooms.get.index')];
        $data['parent'] = $this->parent;
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate()
    {
        authorize('create-classrooms');

        $data['page_title'] = trans('app.Create') . ' ' . trans('app.SpecialClassroom');
        $data['breadcrumb'] = [$this->title => route('school-branch-supervisor.classrooms.get.create')];
        $branch = auth()->user()->schoolAccountBranchType;

        $data['gradeClasses'] = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten()->pluck('title', 'id');
        $data['educationalSystems'] = $branch->educationalSystems->pluck('name', 'id');
        $data['academicYears'] = $branch->branchEducationalSystem()->with('academicYear')->get()->pluck('academicYear')->flatten()->pluck('title', 'id');
        $data['educationalTerms'] = $branch->branchEducationalSystem()->with('educationalTerm')->get()->pluck('educationalTerm')->flatten()->pluck('title', 'id');

        $data['parent'] = $this->parent ;
        return view($this->parent . '.' . $this->module . '.create', $data);
    }

    public function postCreate(SpecialClassRoomRequest  $request)
    {

        authorize('create-classrooms');

        $branchId = auth()->user()->schoolAccountBranchType->id;

        $branchEducationalSystemId = BranchEducationalSystem::where('branch_id', $branchId)
            ->where('educational_system_id', $request->educational_system_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('educational_term_id', $request->educational_term_id)
            ->first()->id;


        $branchEduSysGradeClassId = BranchEducationalSystemGradeClass::where('grade_class_id', $request->grade_class_id)
            ->where('branch_educational_system_id', $branchEducationalSystemId)->first()->id;

        $newClassRoom = Classroom::create([
            'name' => $request->name,
            'branch_id' => $branchId,
            'branch_edu_sys_grade_class_id' => $branchEduSysGradeClassId,
            'is_special' => true
        ]);

        $newClassRoom->specialStudents()->sync($request->students);

        flash()->success(trans('app.Created successfully'));
        return redirect()->route('school-branch-supervisor.specialClassrooms.get.index');
    }

    public function getEdit($id)
    {
        authorize('update-classrooms');

        $data['row'] = Classroom::where('id',$id)->with('specialStudents')->first();

        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;

        $branchEducationalSystemGradeClass = $data['row']->branchEducationalSystemGradeClass;
        $branchEducationalSystem = $branchEducationalSystemGradeClass->branchEducationalSystem;
        $data['row']['gradeClass'] = $branchEducationalSystemGradeClass->gradeClass->id;
        $data['row']['educationalSystem'] = $branchEducationalSystem->educational_system_id;
        $data['row']['academicYear'] = $branchEducationalSystem->academic_year_id;
        $data['row']['educationalTerm'] = $branchEducationalSystem->educational_term_id;

        $data['students'] = Student::where('class_id',$branchEducationalSystemGradeClass->gradeClass->id)->with('user')->has('user')->get();


        $branch = auth()->user()->schoolAccountBranchType;
        $data['gradeClasses'] = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten()->pluck('title', 'id');
        $data['educationalSystems'] = $branch->educationalSystems->pluck('name', 'id');
        $data['academicYears'] = $branch->branchEducationalSystem()->with('academicYear')->get()->pluck('academicYear')->flatten()->pluck('title', 'id');
        $data['educationalTerms'] = $branch->branchEducationalSystem()->with('educationalTerm')->get()->pluck('educationalTerm')->flatten()->pluck('title', 'id');
        $data['parent'] = $this->parent ;
        return view($this->parent . '.' . $this->module . '.edit', $data);
    }

    public function putEdit(Request $request,$id)
    {
        authorize('update-classrooms');

        $data = $request->all();
        $classroom = Classroom::find($id);


        $classroom->update([
            'name' => $request->name,
        ]);

        $classroom->specialStudents()->sync($request->students);

        flash()->success(trans('app.Update successfully'));
        return redirect()->route('school-branch-supervisor.specialClassrooms.get.index');
    }
}
