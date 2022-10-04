<?php


namespace App\OurEdu\SchoolAdmin\InstructorAttendance\Repositories;


use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Builder;
use App\OurEdu\BaseApp\Traits\Filterable;
use Illuminate\Database\Eloquent\Collection;


class ClassroomRepository
{

    use Filterable;

    /**
     * @param  $schoolAccountId
     * @param array $data
     */
    public function teacherAttendance($schoolAccountId, array $data = [])
    {
        $schoolAccount = SchoolAccount::findOrFail($schoolAccountId);
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
            ])
            ->paginate(env("PAGE_LIMIT", 20));

        return $teachersAttendance;
    }

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
     * @return Collection
     */
    public function listClassroomsNamesIDs(int $branch)
    {
        return $classrooms = Classroom::query()
            ->where("branch_id", "=", $branch)
            ->pluck("name", 'id');
    }
}
