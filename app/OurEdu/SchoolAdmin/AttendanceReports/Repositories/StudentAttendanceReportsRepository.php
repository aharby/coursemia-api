<?php


namespace App\OurEdu\SchoolAdmin\AttendanceReports\Repositories;

use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAdmin\Models\SchoolAdmin;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class StudentAttendanceReportsRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new SchoolAccountBranch();
    }

    public function getUserAttends(SchoolAccount $schoolAccount): LengthAwarePaginator
    {
        $availableUserTypes[] = UserEnums::SCHOOL_LEADER;
        $availableUserTypes[] = UserEnums::SCHOOL_SUPERVISOR;
        $availableUserTypes[] = UserEnums::ACADEMIC_COORDINATOR;
        $availableUserTypes[] = UserEnums::EDUCATIONAL_SUPERVISOR;

        $date = $this->handleSearchDate();

        $users = User::query()->where(function (Builder $query) use ($schoolAccount) {
            $query->whereHas("branches", function (Builder $builder) use ($schoolAccount) {
                $builder->whereIn('branch_id', $schoolAccount->branches->pluck('id')->toArray());
            })->orWhereHas("branch", function (Builder $builder) use ($schoolAccount) {
                $builder->where("school_account_id", "=", $schoolAccount->id);
            })
                ->orWhereHas("schoolSupervisor", function (Builder $builder) use ($schoolAccount) {
                    $builder->where("school_account_id", "=", $schoolAccount->id);
                })
                ->orWhereHas("schoolLeader", function (Builder $builder) use ($schoolAccount) {
                    $builder->where("school_account_id", "=", $schoolAccount->id);
                });
        })
            ->when(request()->filled("branch"), function (Builder $query) {
                $query->whereHas('branches', function (Builder $builder) {
                    $builder->where('branch_id', '=', request()->get("branch"));
                });
            })
            ->when(request()->filled("type"), function (Builder $query) {
                $query->where("type", "=", request()->get("type"));
            })
            ->whereIn("type", $availableUserTypes)
            ->withCount([
                'VCRSessionsPresence' => function ($query) use ($date) {
                    $query->whereHas('vcrSession', function ($q) use ($date) {
                        $q->where("time_to_end", "<=", $date['to']);
                        $q->when($date["from"], function (Builder $query) use ($date) {
                            $query->where("time_to_end", ">=", $date['from']);
                        });
                        $q->when(request()->filled("branch"), function (Builder $query) {
                            $query->whereHas('classroom.branch', function ($qu) {
                                $qu->where('id', request('branch'));
                            });
                        });
                        $q->when(request()->filled("classroom"), function (Builder $query) {
                            $query->where("classroom_id", "=", request("classroom"));
                        });
                        $q->when(request()->filled("subject"), function (Builder $query) {
                            $query->where("subject_id", "=", request("subject"));
                        });
                    });
                }
            ])
            ->with(
                [
                    'VCRSessionsPresence',
                    'VCRSessionsPresence.vcrSession.instructor',
                    'VCRSessionsPresence.vcrSession.subject',
                    'VCRSessionsPresence.vcrSession.classroom',
                    'VCRSessionsPresence.vcrSession.classroomClassSession'
                ]
            )
            ->with([
                'VCRSessionsPresence' => function ($vsrSession) use ($date) {
                    $vsrSession->whereHas('vcrSession', function ($session) use ($date) {
                        $session->where("time_to_end", "<=", $date['to']);
                        $session->when($date["from"], function (Builder $query) use ($date) {
                            $query->where("time_to_end", ">=", $date['from']);
                        });
                        $session->when(request()->filled("branch"), function (Builder $query) {
                            $query->whereHas('classroom.branch', function ($qu) {
                                $qu->where('id', request('branch'));
                            });
                        });
                        $session->when(request()->filled("classroom"), function (Builder $query) {
                            $query->where("classroom_id", "=", request("classroom"));
                        });
                        $session->when(request()->filled("subject"), function (Builder $query) {
                            $query->where("subject_id", "=", request("subject"));
                        });
                    });
                }
            ]);

        return $users->paginate(env('PAGE_LIMIT', 20));
    }

    public function handleSearchDate()
    {
        $from = null;
        if (request()->filled("from")) {
            $from = Carbon::parse(request()->get("from"));
            $from->format("Y-m-d 00:00:00");
        }

        $to = Carbon::now();
        $to = $to->format('Y-m-d H:i:s');
        if (request()->filled("to")) {
            $to = Carbon::parse(request()->get("to"));
            $to = $to->format('Y-m-d 23:59:00');
            if (Carbon::today()->lte($to)) {
                $to = Carbon::now();
                $to = $to->format('Y-m-d H:i:s');
            }
        }

        return ['from' => $from, 'to' => $to];
    }
}
