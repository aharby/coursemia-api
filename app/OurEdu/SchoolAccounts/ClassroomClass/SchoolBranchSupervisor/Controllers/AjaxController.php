<?php
namespace App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use App\OurEdu\SchoolAccounts\ClassroomClass\Repositories\ClassroomClassRepositoryInterface;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Student\Transformers\ClassroomClassSessionsTransformer;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AjaxController extends BaseApiController
{
    /**
     * @var ClassroomClassRepositoryInterface
     */
    private $classroomClassRepository;
    /**
     * @var ClassroomRepositoryInterface
     */
    private $classroomRepository;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    /**
     * AjaxController constructor.
     * @param ClassroomClassRepositoryInterface $classroomClassRepository
     * @param ClassroomRepositoryInterface $classroomRepository
     * @param TokenManagerInterface $tokenManager
     */
    public function __construct(ClassroomClassRepositoryInterface $classroomClassRepository, ClassroomRepositoryInterface $classroomRepository, TokenManagerInterface $tokenManager)
    {
        $this->classroomClassRepository = $classroomClassRepository;
        $this->classroomRepository = $classroomRepository;
        $this->tokenManager = $tokenManager;
    }

    public function getSubjectInstructors(SchoolAccountBranch $branch = null){
        if ($subjectID = request('subject_id')) {

            $branch = $branch ?? auth()->user()->schoolAccountBranchType;
            $instructors = Subject::findOrFail($subjectID)->schoolInstructors->whereNotNull('branch_id')->where('branch_id',$branch->id)->where('is_active',1)->pluck('name' , 'id');

            return response()->json(
                [
                    'status' => '200',
                    'instructors' => $instructors
                ]
            );
        }
    }

    public function getInstructorSubjects(User $instructor)
    {
        $subjects = $instructor->schoolInstructorSubjects();

        if(request()->has('gradeClass')){
            $subjects = $subjects->where('grade_class_id',request()->gradeClass);
        }

        if(request()->has('classroom')){
            $subjects = $subjects->whereHas('classroomClasses',function($query){
                $query->where('classroom_id',request()->classroom);
            });
        }

        $subjects = $subjects->get();

        return response()->json([
            "status" => 200,
            'subjects' => $subjects
        ]);
    }


    public function getGradeSubjects(GradeClass $gradeClass)
    {
        $subjects = Subject::query()
            ->where("grade_class_id", "=", $gradeClass->id)
            ->get();

        return response()->json([
            "status" => 200,
            'subjects' => $subjects
        ]);
    }

    public function getClassRoomTimetable(Request $request, $classId)
    {
        $rules = [
            "from" => "nullable|date",
            "to" => "nullable|date",
        ];

        $this->validate($request, $rules);

        $from = $request->get("from", Carbon::today());

        if ($request->filled("from")) {
            $from = Carbon::parse($from);
        }

        if ($request->filled("to")) {
            $to = Carbon::parse($request->get("to"));
        } else {
            $to = $from->copy()->addDays(7);
        }

        $from = $from->format("Y-m-d 00:00:00");
        $to = $to->format("Y-m-d 23:59:00");
        $params['token'] = $this->tokenManager->createAuthToken(TokenNameEnum::DYNAMIC_lINKS_Token);

        $sessions = VCRSession::query()
            ->where('classroom_id' , $classId)
            ->whereDate('time_to_start' , '>=' , $from)
            ->whereDate('time_to_end' , '<=' , $to)
            ->with([
                'classroom','classroomClassSession','instructor','beforeSessionQuizzes','afterSessionQuizzes',
                'subject.educationalSystem','subject.academicalYears','subject.gradeClass'
            ])
            ->orderBy('time_to_start','ASC')
            ->get();

        return $this->transformDataModInclude($sessions , 'subject,classroom,instructor' ,new ClassroomClassSessionsTransformer($params) , ResourceTypesEnums::CLASSROOM_CLASS_SESSION);
   }

    function getSessionUrlForSuperVisor($sessionId,$classroomId)
    {
        $token = $this->tokenManager->createAuthToken(TokenNameEnum::DYNAMIC_lINKS_Token);
        //return $credentials;
        $session = VCRSession::where('classroom_id' , $classroomId)
            ->where('id',$sessionId)
            ->first();
        $url = getDynamicLink(DynamicLinksEnum::SUPERVISOR_JOIN_ROOM,
            ['session_id' => $session->id,
                'token' => $token,
                'type' => $session->vcr_session_type,
                'portal_url' => env('VCR_PORTAL_URL','https://vcr.ta3lom.com')
            ]);

        return \response()->json([
            'meeting_type' => $session->meeting_type,
            'sessionId' => $session->id,
            'joinUrl' => $url,
            'token' => $token,
            'local' => app()->getLocale(),
            'baseApp' => env('APP_URL'.'/api/v1','https://admin.ta3lom.com/api/v1')
                                 ]);
    }

    public function getClassroomStudents()
    {

        $this->validate(request(),[
           'grade_class_id' => 'required',
        ]);
        $branchId = auth()->user()->schoolAccountBranchType->id;

        $classroomIds = Classroom::where('branch_id', $branchId)->pluck('id');

        $studentQuery = Student::query()->whereIn('classroom_id', $classroomIds)->whereHas('user',function($q){
            $q->where('type',UserEnums::STUDENT_TYPE);
        });
        return $studentQuery
            ->when(request('grade_class_id') != "",function($q){
                $q->where('class_id',request('grade_class_id'));
            })
            ->when(request('educational_system_id') != "",function ($q){
                $q->where('class_id',request('grade_class_id'));
            })
            ->when(request('academic_year_id') != "",function ($q){
                $q->where('academical_year_id',request('academic_year_id'));
            })->with('user')->get();
    }

    public function getBranchClassrooms(SchoolAccountBranch $branch)
    {
        return response()->json([
            "status" => 200,
            "classes" => $this->classroomRepository->listClassroomsNamesIDs($branch->id),
        ]);
    }

    public function geGradeClassrooms(GradeClass $gradeClass, SchoolAccountBranch $branch = null)
    {
        $branch = $branch ?? Auth::user()->branch;
        $classrooms = Classroom::query()
            ->whereHas("branchEducationalSystemGradeClass", function (Builder $branchEducationSystemGrade) use ($gradeClass) {
                $branchEducationSystemGrade->where("grade_class_id", "=", $gradeClass->id);
            })
            ->where("branch_id", "=", $branch->id ?? null)
            ->pluck("name", "id");

        return response()->json([
            "status" => 200,
            "classrooms" => $classrooms,
        ]);
    }

    public function getClassroomClasses(Classroom $classroom)
    {
        return response()->json([
            "status" => 200,
            "classes" => $this->classroomClassRepository->getByClassroom($classroom),
        ]);
    }

    public function getClassSessions(ClassroomClass $class)
    {
        return response()->json([
            "status" => 200,
            "sessions" => $this->classroomClassRepository->getSessions($class),
        ]);
    }

    public function classroomInstructors(Classroom $classroom)
    {
        $instructors = User::query()
            ->whereHas("schoolInstructorSessions" , function (Builder $sessions) use ($classroom) {
                $sessions->where("classroom_id", "=", $classroom->id);
            })
            ->get();

        return response()->json([
            "status" => 200,
            "instructors" => $instructors,
        ]);
    }

    public function classroomInstructorSessions(Request $request)
    {
        $sessions = ClassroomClassSession::query()
            ->with("subject")
            ->where("instructor_id", "=", $request->get("instructor"))
            ->where("classroom_id", "=", $request->get("classroom"));
        if ($request->filled("date")) {
            $sessions->where("from", ">=", Carbon::parse($request->get("date")))
                ->where("to", "<", Carbon::parse($request->get("date"))->addDay());
        }

        $sessions = $sessions->get();


        return response()->json([
            "status" => 200,
            "sessions" => $sessions,
        ]);
    }

    public function getBranchGrade(SchoolAccountBranch $branch)
    {
        $grades = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten();
        $gradeClasses = [];
        foreach ($grades as $grade) {
            $gradeClasses[$grade->id] = $grade->title;
        }

        return response()->json([
            "status" => 200,
            "gradeClasses" => $gradeClasses,
        ]);
    }
}
