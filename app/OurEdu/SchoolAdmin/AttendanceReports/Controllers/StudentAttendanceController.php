<?php


namespace App\OurEdu\SchoolAdmin\AttendanceReports\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\UserAttendanceExport;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAdmin\AttendanceReports\Exports\UserPresenceSessionsExport;
use App\OurEdu\SchoolAdmin\Middleware\SchoolAdminMiddleware;
use App\OurEdu\SchoolAdmin\AttendanceReports\Repositories\StudentAttendanceReportsRepository;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Maatwebsite\Excel\Facades\Excel;

class StudentAttendanceController extends BaseController
{
    /**
     * @var string
     */
    private $module;
    /**
     * @var string|null
     */
    private $title;
    /**
     * @var string
     */
    private $parent;
    /**
     * @var StudentAttendanceReportsRepository
     */
    private $repository;

    public function __construct()
    {
        $this->module = 'attendance_reports';
        $this->title = trans('app.Student Reports');
        $this->repository = new StudentAttendanceReportsRepository();
        $this->parent = ParentEnum::SCHOOL_ADMIN;
        $this->middleware(SchoolAdminMiddleware::class);
    }

    public function getUserAttends()
    {
        $user = auth()->user();
        $schoolAccount = $user->schoolAdmin->currentSchool;
        $schoolBranches = $schoolAccount->branches->pluck("name", 'id') ?? [];

        $filterableTypes = [
            UserEnums::SCHOOL_LEADER => trans('app.' . UserEnums::SCHOOL_LEADER),
            UserEnums::SCHOOL_SUPERVISOR => trans('app.' . UserEnums::SCHOOL_SUPERVISOR),
            UserEnums::ACADEMIC_COORDINATOR => trans('app.' . UserEnums::ACADEMIC_COORDINATOR),
            UserEnums::EDUCATIONAL_SUPERVISOR => trans('app.' . UserEnums::EDUCATIONAL_SUPERVISOR),
        ];

        $userAttends = $this->repository->getUserAttends($schoolAccount);
        $data['branch'] = request()->get('branch');
        if (!is_null($data['branch'])) {
            $data['branch'] = $schoolAccount->branches->where('id', $data['branch'])->first()->name;
        }
        $data['rows'] = $userAttends;
        $data['schoolBranches'] = $schoolBranches;
        $data['filterableTypes'] = $filterableTypes;
        $data['page_title'] = trans('app.User Attends');
        $data['breadcrumb'] = [$this->title => route('school-account-manager.manager-reports.user-attends')];
        return view($this->parent . '.' . $this->module . '.user_attends', $data);
    }

    public function exportUserAttends()
    {
        $user = auth()->user();
        $schoolAccount = SchoolAccount::find($user->schoolAdmin->current_school_id);

        $userAttends = $this->repository->getUserAttends($schoolAccount)->items();

        return Excel::download(new UserAttendanceExport($userAttends), "user_attendance.xls");
    }

    public function exportUserPresenceSessions(User $user)
    {
        return Excel::download(new UserPresenceSessionsExport($user->VCRSessionsPresence, $this->userPresenceSessionsExportHeader()), $user->name . "_attendance_sessions.xls");
    }

    private function userPresenceSessionsExportHeader()
    {
        return [
            trans('reports.classroom'),
            trans('reports.branch'),
            trans('reports.subject'),
            trans('reports.instructor'),
            trans('reports.from'),
            trans('reports.to'),
            trans('reports.date'),
        ];
    }
}
