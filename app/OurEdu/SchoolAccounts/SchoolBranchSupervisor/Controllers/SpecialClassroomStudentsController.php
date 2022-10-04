<?php


namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Middleware\SchoolSupervisorMiddleware;
use App\OurEdu\Users\Models\Student;

class SpecialClassroomStudentsController extends BaseController
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

    public function index($specialClassroom)
    {
        authorize('view-students');

        $data['rows'] = $this->studentsLookup($specialClassroom)->jsonPaginate(env('PAGE_LIMIT', 20));

        $branchId = auth()->user()->schoolAccountBranchType->id;
        $branch = auth()->user()->schoolAccountBranchType;
        $data['classrooms'] = Classroom::where('branch_id', $branchId)->where('is_special',1)->pluck('name', 'id')->toArray();

        $data['educational_systems'] = EducationalSystem::whereHas('schoolAccountBranches', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })->with('translations')->listsTranslations('name')->pluck('name', 'id')->toArray();
        $data['grade_classes'] = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten()->pluck('title', 'id');
        $data['page_title'] = trans('app.View') .' '.trans('app.Students');
        $data['breadcrumb'] = [$this->title => route('school-branch-supervisor.students.get.index')];
        $data['module'] = $this->module;
        $data['parent'] = $this->parent;
        return view($this->parent . '.' . $this->module . '.students', $data);

    }

    private function studentsLookup($specialClassroom,$parent = false)
    {
        $branchId = auth()->user()->schoolAccountBranchType->id;

        $students = Student::whereHas('specialClassroom',function ($q) use($specialClassroom){
            $q->where('classroom_id',$specialClassroom);
        });

        return $students
                ->when(request('mobile'),function ($query){
                    $query->whereHas('user',function($query){
                        $query->where('mobile','LIKE','%' .request('mobile'). '%' );
                    });
                })->when(request('username'), function ($query) {
                    $query->whereHas('user',function($query){
                        $query->where('username', request('username'));
                    });
                })
                ->when(request('grade_class'), function ($query) {
                    $query->where('class_id', request('grade_class'));
                })->when(request('educational_system'), function ($query) {
                    $query->where('educational_system_id', request('educational_system'));
                });
    }
}
