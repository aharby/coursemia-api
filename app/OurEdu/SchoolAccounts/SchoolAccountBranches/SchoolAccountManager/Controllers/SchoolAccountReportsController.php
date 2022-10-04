<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Exports\UserPresenceSessionsExport;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\UserAttendanceExport;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class SchoolAccountReportsController extends BaseController
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
     * @var SchoolAccountBranchesRepository
     */
    private $repository;

    /**
     * SchoolAccountReportsController constructor.
     * @param SchoolAccountBranchesRepository $schoolAccountBranchesRepository
     */
    public function __construct(
        SchoolAccountBranchesRepository $schoolAccountBranchesRepository
    )
    {
        $this->module = 'reports';
        $this->title = trans('app.Reports');
        $this->parent = ParentEnum::SCHOOL_ACCOUNT_MANAGER;
        $this->repository = $schoolAccountBranchesRepository;
    }

    public function getUserAttends()
    {
        $user = auth()->user();

        $schoolBranches = $user->schoolAccount->branches->pluck("name", 'id') ?? [];
        $filterableTypes = [
            UserEnums::SCHOOL_LEADER => trans('app.'.UserEnums::SCHOOL_LEADER),
            UserEnums::SCHOOL_SUPERVISOR => trans('app.'.UserEnums::SCHOOL_SUPERVISOR),
            UserEnums::ACADEMIC_COORDINATOR => trans('app.'.UserEnums::ACADEMIC_COORDINATOR),
            UserEnums::EDUCATIONAL_SUPERVISOR => trans('app.'.UserEnums::EDUCATIONAL_SUPERVISOR),
        ];

        if($user->type == UserEnums::SCHOOL_ACCOUNT_MANAGER){
            $userAttends = $this->repository->getUserAttends($user->schoolAccount);
            $data['branch'] = request()->get('branch');
            if(!is_null($data['branch'])){
                $data['branch'] = $user->schoolAccount->branches->where('id',$data['branch'])->first()->name;
            }
            $data['rows'] = $userAttends;
            $data['schoolBranches'] = $schoolBranches;
            $data['filterableTypes'] = $filterableTypes;
            $data['page_title'] = trans('app.User Attends');
            $data['breadcrumb'] = [$this->title => route('school-account-manager.manager-reports.user-attends')];
            return view($this->parent.'.'.$this->module.'.user_attends',$data);
        }
    }

    public function exportUserAttends()
    {
        $user = auth()->user();

        if($user->type == UserEnums::SCHOOL_ACCOUNT_MANAGER){
            $userAttends = $this->repository->getUserAttends($user->schoolAccount)->items();

            return Excel::download(new UserAttendanceExport($userAttends),"user_attendance.xls");
        }

        abort(403);
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
