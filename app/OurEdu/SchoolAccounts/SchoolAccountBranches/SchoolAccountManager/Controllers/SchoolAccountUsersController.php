<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Controllers;

use App\OurEdu\Assessments\Jobs\AddUserToAssessmentJob;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Roles\Repository\RoleRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Requests\UsersRequest;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\UseCases\users\SchoolUsersUseCaseInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\UseCases\CreateUserUseCase\CreateUserUseCaseInterface;
use App\OurEdu\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCaseInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolAccountUsersController extends BaseController
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
     * @var RoleRepositoryInterface
     */
    private $roleRepository;
    /**
     * @var SchoolAccountBranchesRepository
     */
    private $schoolAccountBranchesRepository;
    /**
     * @var SchoolUsersUseCaseInterface
     */
    private $schoolUsersUseCase;
    /**
     * @var CreateZoomUserUseCaseInterface
     */
    private $createZoomUser;

    /**
     * SchoolAccountUsersController constructor.
     * @param RoleRepositoryInterface $roleRepository
     * @param SchoolAccountBranchesRepository $schoolAccountBranchesRepository
     * @param SchoolUsersUseCaseInterface $schoolUsersUseCase
     */
    public function __construct(
        RoleRepositoryInterface $roleRepository,
        SchoolAccountBranchesRepository $schoolAccountBranchesRepository,
        SchoolUsersUseCaseInterface $schoolUsersUseCase,
        CreateZoomUserUseCaseInterface $createZoomUser
    )
    {
        $this->module = 'users';
        $this->title = trans('app.School Account Branches');
        $this->parent = ParentEnum::SCHOOL_ACCOUNT_MANAGER;
        $this->roleRepository = $roleRepository;
        $this->schoolAccountBranchesRepository = $schoolAccountBranchesRepository;
        $this->schoolUsersUseCase = $schoolUsersUseCase;
        $this->createZoomUser = $createZoomUser;
    }

    public function show(User $user)
    {
        $userBranches = [$user->branch->name ?? null];

        if ($user->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            $userBranches = $user->branches()->pluck("name")->toArray();
        }

        $data['page_title'] = trans('app.View Users');
        $data['user'] = $user;
        $data['userBranch'] = implode(", ", $userBranches);
        $data['breadcrumb'] = [trans('app.Users')=>route('school-account-manager.school-account-branches.get.users')];

        return view($this->parent . "." . $this->module . ".view", $data);
    }

    public function create()
    {
        $user = auth()->user();
        $schoolAccountBranches =[];
        $schoolAccountRoles =[];
        $educationalSystems = [];
        $gradeClasses = [];
        $userEnum = UserEnums::class;

        if ($user->type == UserEnums::SCHOOL_ACCOUNT_MANAGER) {
            $schoolAccountBranches = $this->schoolAccountBranchesRepository
                ->getBranchesBySchoolAccountManagerPluck($user->id);

            $schoolAccountRoles = $this->roleRepository->pluckSchoolRoles($user->schoolAccount);
        }

        $data['breadcrumb'] = [trans('app.Users') => route('school-account-manager.school-account-branches.get.users')];
        $data['schoolAccountBranches'] = $schoolAccountBranches;
        $data['educationalSystems']=$educationalSystems;
        $data['schoolAccountRoles'] = $schoolAccountRoles;
        $data['gradeClasses']=$gradeClasses;
        $data['page_title'] = trans('app.create_user');

        $data['userEnum'] = $userEnum;

        return view($this->parent . "." . $this->module . ".create", $data);
    }

    public function store(UsersRequest $request)
    {
//        DB::beginTransaction();
        $user = $this->schoolUsersUseCase->create($request);

        $createZoomUser = $this->createZoomUser->createUser($user);
        if ($createZoomUser['error']) {
//            DB::rollBack();
            Log::channel('slack')->error($createZoomUser['detail']);
//            flash()->error($createZoomUser['detail'] );
//            return redirect()->back();
        }

//        DB::commit();
        AddUserToAssessmentJob::dispatch($user)->afterCommit();
        return redirect(route("school-account-manager.school-account-branches.get.users"))
            ->with("success", trans("school-account-users.created successfully"));
    }
}
