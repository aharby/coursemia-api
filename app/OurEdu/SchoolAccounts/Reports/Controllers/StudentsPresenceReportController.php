<?php


namespace App\OurEdu\SchoolAccounts\Reports\Controllers;


use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\Reports\Exports\ClassroomAttendanceExport;
use App\OurEdu\SchoolAccounts\Reports\Exports\StudentsAttendanceExport;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepository;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSessionPresence;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class StudentsPresenceReportController
{
    /**
     * @var string
     */
    private $module;
    /**
     * @var string
     */
    private $parent;
    /**
     * @var ClassroomRepositoryInterface
     */
    private $classroomRepository;
    private $title;

    /**
     * StudentsPresenceReportController constructor.
     * @param ClassroomRepositoryInterface $classroomRepository
     */
    public function __construct(ClassroomRepositoryInterface $classroomRepository)
    {
        $this->module = 'reports';
        $this->title = trans('navigation.Students class Presence');
        $this->parent = ParentEnum::SCHOOL_SUPERVISOR;
        $this->classroomRepository = $classroomRepository;
    }

    public function classPresence(Request $request, SchoolAccountBranch $branch)
    {
        $branch_id = $branch->id ?? auth()->user()->branch_id;
        $classrooms = Classroom::query()
            ->where("branch_id", "=", $branch_id)
            ->pluck("name", 'id');

        $data['branch'] = $branch_id;
        $data['page_title'] = $this->title;
        $data['classrooms'] = $classrooms;
        $data["rows"] = $this->classroomRepository->classPresenceReport($request, $branch);

        return view($this->parent . "." . $this->module . ".students-presence-report", $data);
    }

    public function exportClassPresence(Request $request, SchoolAccountBranch $branch = null)
    {

        $branch_id = $branch->id ?? auth()->user()->branch_id;
        $classrooms = Classroom::query()
            ->where("branch_id", "=", $branch_id)
            ->pluck("name", 'id');


        $getClassrooms = $request->filled("classroom") ? $this->classroomRepository->classPresenceReport($request, $branch)->items() : [];

        $from = null;
        if ($request->filled("from")) {
            $from = Carbon::parse($request->get("from"));
            $from->format("Y-m-d 00:00:00");
        }

        $to = Carbon::now();
        $to = $to->format('Y-m-d H:i:s');
        if ($request->filled("to")) {
            $to = Carbon::parse($request->get("to"));
            $to = $to->format('Y-m-d 23:59:00');
            if (Carbon::today()->lte($to)) {
                $to = Carbon::now();
                $to = $to->format('Y-m-d H:i:s');
            }
        }

        $fileName =  "classroom_attendance_{$request->get('classroom')}_from_" . $from . "_to_" . $to . ".xls";

        return Excel::download(new ClassroomAttendanceExport($getClassrooms), $fileName);
    }

    public function subjectsPresence (Request $request, SchoolAccountBranch $branch)
    {
        $branch_id = $branch->id ?? \auth()->user()->branch_id;
        $classRoom = Classroom::query()
            ->where("branch_id", "=", $branch_id)
            ->find($request->get("classroom"));

        $classrooms = Classroom::query()
            ->where("branch_id", "=", $branch_id)
            ->pluck("name", 'id');

        $date = Carbon::now();
        $to = $date->format("Y-m-d H:i:s");

        if ($request->filled("date")) {
            $date = Carbon::parse($request->get("date"));
            $to = $date->format("Y-m-d 23:59:00");

            if (Carbon::today()->lte($date)) {
                $date = Carbon::now();
                $to = $date->format("Y-m-d H:i:s");
            }
        }
        $from = $date->format("Y-m-d 00:00:00");

        $data["page_title"] = trans('navigation.Students Subjects Presence');
        $data["classrooms"] = $classrooms;
        $data["classSessions"] = [];
        $data["students"] = [];
        $data["branch"] = $branch_id;

        if (!$classRoom) {

            if ($request->filled("classroom")) {
                abort(404);
            }

            return view($this->parent . "." . $this->module . ".students-subjects-presence", $data);
        }

        $students = User::query()
            ->where("is_active", "=", true)
            ->whereHas("student", function (EBuilder $studentsQuery) use ($classRoom, $from, $to) {
                $studentsQuery->where("classroom_id", "=", $classRoom->id);
                $studentsQuery->whereHas("classroom.sessions", function (EBuilder $classSessions) use ($classRoom, $from, $to) {
                        $classSessions->where("from", ">=", $from);
                        $classSessions->where("to", "<=", $to);
                    });
            })

            ->with(["VCRSessionsPresence" => function (HasMany $vcrPresence) use ($classRoom) {
                $vcrPresence->with(["vcrSession" => function (BelongsTo $vcrSession) use ($classRoom) {
                    $vcrSession->where("classroom_id", "=", $classRoom->id);
                }])
                ->whereHas("vcrSession", function (EBuilder $vcrSession) use ($classRoom) {
                    $vcrSession->where("classroom_id", "=", $classRoom->id);
                })
                ->get();
            }])->get();

        foreach ($students as $student) {
            $attendedSessions = [];
            foreach ($student->VCRSessionsPresence as $vcrPresence) {
                $attendedSessions[] = $vcrPresence->vcrSession->classroom_session_id;
            }
            $student->attendSessions = $attendedSessions;
        }

        $classSessions = $classRoom->sessions()
            ->with("subject")
            ->where("from", ">=", $from)
            ->where("to", "<=", $to)
            ->get();


        $data["classSessions"] = $classSessions;
        $data["students"] = $students;

        return view($this->parent . "." . $this->module . ".students-subjects-presence", $data);
    }

    public function ExportSubjectsPresence (Request $request, SchoolAccountBranch $branch)
    {
        $branch_id = $branch->id ?? \auth()->user()->branch_id;
        $classRoom = Classroom::query()
            ->where("branch_id", "=", $branch_id)
            ->find($request->get("classroom"));


        $date = Carbon::now();
        $to = $date->format("Y-m-d H:i:s");

        if ($request->filled("date")) {
            $date = Carbon::parse($request->get("date"));
            $to = $date->format("Y-m-d 23:59:00");

            if (Carbon::today()->lte($date)) {
                $date = Carbon::now();
                $to = $date->format("Y-m-d H:i:s");
            }
        }
        $from = $date->format("Y-m-d 00:00:00");


        if (!$classRoom) {

            if ($request->filled("classroom")) {
                abort(404);
            }

            return "";
        }

        $students = User::query()
            ->where("is_active", "=", true)
            ->whereHas("student", function (EBuilder $studentsQuery) use ($classRoom, $from, $to) {
                $studentsQuery->where("classroom_id", "=", $classRoom->id);
                $studentsQuery->whereHas("classroom.sessions", function (EBuilder $classSessions) use ($classRoom, $from, $to) {
                    $classSessions->where("from", ">=", $from);
                    $classSessions->where("to", "<=", $to);
                });
            })

            ->with(["VCRSessionsPresence" => function (HasMany $vcrPresence) use ($classRoom) {
                $vcrPresence->with(["vcrSession" => function (BelongsTo $vcrSession) use ($classRoom) {
                    $vcrSession->where("classroom_id", "=", $classRoom->id);
                }])
                    ->whereHas("vcrSession", function (EBuilder $vcrSession) use ($classRoom) {
                        $vcrSession->where("classroom_id", "=", $classRoom->id);
                    })
                    ->get();
            }])->get();

        foreach ($students as $student) {
            $attendedSessions = [];
            foreach ($student->VCRSessionsPresence as $vcrPresence) {
                $attendedSessions[] = $vcrPresence->vcrSession->classroom_session_id;
            }
            $student->attendSessions = $attendedSessions;
        }

        $classSessions = $classRoom->sessions()
            ->with("subject")
            ->where("from", ">=", $from)
            ->where("to", "<=", $to)
            ->get();

        $fileName =  "Students_Subjects_Presence" . "_" . $from . "_" . $to . ".xls";

        return Excel::download(new StudentsAttendanceExport($students, $classSessions, $this->exportHeadings($classSessions)), $fileName);
    }

    /**
     * @param Collection $classSessions
     * @return array
     */
    private function exportHeadings(Collection $classSessions)
    {
        $heading = [
            trans('students.student name'),
            trans('students.ID'),];

        foreach($classSessions as $session) {
            $heading[] = $session->subject->name . " " . $session->from->format("h:i") . " - " . $session->to->format("h:i");
        }

        return $heading;
    }

}
