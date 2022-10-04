<?php


namespace App\OurEdu\SchoolAccounts\Repositories;


use App\OurEdu\SchoolAccounts\BranchEducationalSystem;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ClassroomRepository implements ClassroomRepositoryInterface
{

    use Filterable;

    /**
     * @return Classroom
     */
    public function find($id): ?Classroom
    {
        return Classroom::query()
            ->where("id", "=", $id)
            ->first();
    }

    /**
     * @param $branchId
     * @return Builder[]|Collection
     */
    public function getBranchClassroomClasses($branchId)
    {
        return Classroom::query()
            ->where("branch_id", "=", auth()->user()->branch_id)
            ->get();
    }

    public function classPresenceReport(Request $request, SchoolAccountBranch $branch = null) : LengthAwarePaginator
    {
        $branch_id = $branch->id ?? auth()->user()->branch_id;
        // TODO: Need to optimize the nested queries
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
//        $to = $to->format('Y-m-d H:i:s');

        $classrooms = Classroom::query()
            ->with(['students' => function ($query) use ($to, $from, $request) {
                $query->whereHas('user', function (Builder $users) {
                    $users->where("is_active", "=", true);
                });
                $query->with(["user" => function ($users) use ($to, $from, $request) {

                    $users->withCount([
                        'VCRSessionsPresence' => function ($query) use ($from, $to, $request) {
                            $query->whereHas('vcrSession', function ($q) use ($from, $to, $request) {
                                $q->where("time_to_end", "<=", $to);
                                $q->when("$from", function (Builder $query) use ($from) {
                                    $query->where("time_to_end", ">=", $from);
                                });
                                $q->when($request->filled("classroom"), function (Builder $query) use ($request) {
                                    $query->where("classroom_id", "=", $request->get("classroom"));
                                });
                            });
                        }
                    ]);
                }]);
            }])
            ->withCount(["sessions as sessions_count" => function (Builder $sessions) use ($to, $from) {

                $sessions->where("from", "<=", $to);
                $sessions->when("$from", function (Builder $query) use ($from) {
                    $query->where("to", ">=", $from);
                });
            }])
            ->where("branch_id", "=", $branch_id);

        if ($request->filled("classroom")) {
            $classrooms->where("id", "=", $request->get("classroom"));
        }

        if ($from) {
            $classrooms->whereHas("sessions", function (Builder $sessions) use ($request, $from) {
                $sessions->where("to", ">=", $from);
            });
        }

        $classrooms->whereHas("sessions", function (Builder $sessions) use ($to) {
            $sessions->where("to", "<=", $to);
        });

        return $classrooms->jsonPaginate(20);
    }


    public function getBranchClassroomsByIds($classroomIds,$branchId,$subjectId=null){
        $query = Classroom::query()
                    ->where("branch_id", "=", $branchId)
                    ->whereIn('id',$classroomIds);
        if(!is_null($subjectId)){
            $query->whereHas('classroomClass',function($q) use($subjectId){
                $q->where('subject_id',$subjectId);
            });
        }
        return $query->pluck('id')->toArray();
    }


    public function getClassroomsByBranchAndGradeclass($branchId, $gradeClassId, $subjectId = null): array
    {
        return $this->getClassroomsByBranchesAndGradeClasses([$branchId], $gradeClassId, $subjectId);
    }


    public function getClassroomsByBranchesAndGradeClasses(array $branches, int $gradeClassId, int $subjectId = null, int $educationalSystemId = null): array
    {
        $classrooms = Classroom::query()
        ->whereHas(
            'branchEducationalSystemGradeClass',
            function ($q) use ($gradeClassId) {
                $q->where('grade_class_id', '=', $gradeClassId);
            }
        )
        ->whereIn('branch_id', $branches);

        if (!is_null($subjectId)) {
            $classrooms->whereHas(
                'classroomClass',
                function ($q) use ($subjectId) {
                    $q->where('subject_id', $subjectId);
                }
            );
        }

        if (!is_null($educationalSystemId)) {
            $branchEducationalSystem = BranchEducationalSystem::query()
                ->whereIn("branch_id", $branches)
                ->where("educational_system_id", "=", $educationalSystemId)
                ->pluck("id");

            $classrooms->whereHas(
                "branchEducationalSystemGradeClass",
                function (Builder $branchEducationalSystemGradeClass) use ($branchEducationalSystem) {
                    $branchEducationalSystemGradeClass->whereIn("branch_educational_system_id", $branchEducationalSystem);
                }
            );
        }

        return $classrooms->pluck('id')->toArray();
    }

    /**
     * @return Collection
     */
    public function listClassroomsNamesIDs(int $branch)
    {
        return $classrooms = Classroom::query()
            ->where("branch_id", "=", $branch)
            ->pluck("name", 'id');
    }

    /**
     * @param SchoolAccount $schoolAccount
     * @param array $data
     */
    public function teacherAttendance(SchoolAccount $schoolAccount, array $data = [],$paginate = true)
    {
        $teachersAttendance = User::query()
            ->where("type", "=", UserEnums::SCHOOL_INSTRUCTOR)
            ->whereHas("branch", function (Builder $branch) use ($schoolAccount, $data) {
                $branch->when(isset($data["branch"]), function (Builder $query) use ($data) {
                    $query->where("id", "=", $data["branch"]);
                });
                $branch->where("school_account_id", "=", $schoolAccount->id);
            })
            ->with("branch")
            ->withCount([
                "schoolInstructorSessions" => function (Builder $classroomClassSession) use ($data) {

                    if (isset($data['from'])) {
                        $classroomClassSession->where("from", ">=", $data["from"]);
                    }

                    if (isset($data['to'])) {
                        $classroomClassSession->where("to", "<=", $data["to"]);
                    }
                },
                "VCRSessionsPresence" => function (Builder $VCRSessionPresence) use ($data) {

                    if (isset($data['from'])) {
                        $VCRSessionPresence->where("session_time_to_start", ">=", $data["from"]);
                    }

                    if (isset($data['to'])) {
                        $VCRSessionPresence->where("session_time_to_end", "<=", $data["to"]);
                    }
                },
            ]);

            if($paginate){
                return $teachersAttendance->paginate(env("PAGE_LIMIT", 20));
            }
        return $teachersAttendance->get();
    }
}
