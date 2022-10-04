<?php


namespace App\OurEdu\SchoolAccounts\Reports\Controllers;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\SchoolAccounts\Reports\Exports\TeacherSessionsAttendanceExport;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class TeacherAttendanceController extends BaseController
{
    /**
     * @var ClassroomRepositoryInterface
     */
    private $classroomRepository;
    /**
     * @var string
     */
    private $title;

    /**
     * TeacherAttendanceController constructor.
     * @param ClassroomRepositoryInterface $classroomRepository
     */
    public function __construct(ClassroomRepositoryInterface $classroomRepository)
    {
        $this->classroomRepository = $classroomRepository;
        $this->title = trans("instructors.Instructor Attendance");
    }

    public function teacherSessionAttendance(Request $request)
    {
        $this->validate($request, [
            "from" => "nullable|date|before:now",
            "to" => "nullable|date|after_or_equal:from|before:now"
        ]);

        $requestData = $request->all();

        if ($request->filled("from")) {
            $requestData["from"] = Carbon::parse($request->get("from"))->format("Y-m-d 00:00");
        }
        if (!$request->filled("to") || ($request->filled("to") && Carbon::yesterday()->lt(Carbon::parse($request->get("to"))))) {
            $requestData["to"] = Carbon::now();
        } else {
            $requestData["to"] = Carbon::parse($request->get("to"))->format("Y-m-d 23:59");
        }

        $data["instructors"] = $this->classroomRepository->teacherAttendance(Auth::user()->schoolAccount, $requestData);
        $data["branches"] = Auth::user()->schoolAccount->branches()->pluck('name', 'id')->toArray();
        $data['page_title'] = $this->title;

        return view("school_account_manager.instructorAttendance.instructor_attendance", $data);
    }

    public function exportTeacherSessionAttendance(Request $request)
    {
        $this->validate($request, [
            "from" => "nullable|date|before:now",
            "to" => "nullable|date|after_or_equal:from|before:now"
        ]);

        $requestData = $request->all();

        if ($request->filled("from")) {
            $requestData["from"] = Carbon::parse($request->get("from"))->format("Y-m-d 00:00");
        }
        if (!$request->filled("to") || ($request->filled("to") && Carbon::yesterday()->lt(Carbon::parse($request->get("to"))))) {
            $requestData["to"] = Carbon::now();
        } else {
            $requestData["to"] = Carbon::parse($request->get("to"))->format("Y-m-d 23:59");
        }

        $instructors = $this->classroomRepository->teacherAttendance(Auth::user()->schoolAccount, $requestData, false);
        

        $fileName = "teachers_attendance";

        if (isset($requestData["branch"])) {
            $fileName .= "_branch_" . $requestData["branch"];
        }

        if (isset($requestData["from"])) {
            $fileName .= "_from_" . $requestData["from"];
        }

        $fileName .= "_to_".$requestData["to"];
        $fileName.= "_page_" . $request->get("page") ?? 1;
        $fileName .= ".xls";

        return Excel::download(new TeacherSessionsAttendanceExport($instructors), $fileName);
    }
}
