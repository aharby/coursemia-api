<?php
namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Controllers;

use App\OurEdu\Roles\Repository\RoleRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Requests\SchoolAccountBranchRequest;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Requests\SchoolAccountBranchUpdateBranchesRequest;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Requests\SchoolAccountBranchUpdatePasswordsRequest;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Requests\SetRoleRequest;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountManager\Middleware\SchoolSupervisorMiddleware;
use App\OurEdu\Users\UseCases\SendActivationMailUseCase\SendActivationMailUseCaseInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Middleware\SchoolAccountBranchMiddleware;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\UseCases\SchoolAccountBranchUseCase\SchoolAccountBranchUseCaseInterface;
use Illuminate\Support\Facades\Auth;

class SchoolAccountBranchesController extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $userRepository;
    private $sendActivationMailUseCase;
    private $schoolAccountBranchUseCase;
    private $educationalSystemRepository;
    private $roleRepository;

    public function __construct(
        SchoolAccountBranchesRepository $schoolAccountBranchesRepository,
        UserRepositoryInterface $userRepository,
        SendActivationMailUseCaseInterface $activationMailUseCase,
        SchoolAccountBranchUseCaseInterface $schoolAccountBranchUseCase,
        EducationalSystemRepositoryInterface $educationalSystemRepository,
        RoleRepositoryInterface $roleRepository
    )
    {
        $this->module = 'school_account_branches';
        $this->title = trans('app.School Account Branches');
        $this->repository = $schoolAccountBranchesRepository;
        $this->parent = ParentEnum::SCHOOL_ACCOUNT_MANAGER;
        $this->userRepository = $userRepository;
        $this->sendActivationMailUseCase = $activationMailUseCase;
        $this->schoolAccountBranchUseCase = $schoolAccountBranchUseCase;
        $this->educationalSystemRepository = $educationalSystemRepository;
        $this->roleRepository = $roleRepository;
        $this->middleware(SchoolAccountBranchMiddleware::class);

    }
    public function getIndex()
    {
        $user = auth()->user();
        if($user->type == UserEnums::SCHOOL_ACCOUNT_MANAGER){
            $data['rows'] = $this->repository->getBranchesBySchoolAccountManagerPaginate($user->id);
        }
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getEdit($id)
    {
        $branch = $this->repository->findWith($id,['schoolAccount','educationalSystems']);
        $data['row'] = $branch;
        $data['page_title'] = trans('app.Edit').' '.$this->title;
        $data['breadcrumb'] = [$this->title => route('admin.school-account-branches.get.index')];
        $data['educationalSystems'] = $this->educationalSystemRepository->pluckBySchoolAccountId($branch->schoolAccount->id);
        return view($this->parent.'.'.$this->module.'.edit',$data);
    }


    public function putEdit(SchoolAccountBranchRequest $request,$id)
    {
            if (!$this->roleRepository->getSchoolDefaultRole(Auth::user()->schoolAccount)) {
                return redirect()->back()->with("error" , trans('you have to add at least one role firstly'));
            }

        $requestData = $request->all();
        $schoolAccountResult = $this->schoolAccountBranchUseCase->update($requestData, $id);

        if ($schoolAccountResult['error']) {
            flash()->error($schoolAccountResult['detail'] ?? trans('app.Oopps Something is broken'));
            return redirect()->back();

        }
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('school-account-manager.school-account-branches.get.index');

    }

    public function getView($id)
    {
        $data['row'] = $this->repository->findWith($id,['supervisor','leader']);
        $data['page_title'] = trans('app.View').' '.$this->title;
        $data['breadcrumb'] = [$this->title => route('school-account-manager.school-account-branches.get.view',$id)];
        return view($this->parent.'.'.$this->module.'.view',$data);
    }


    public function getUpdatePassword($id)
    {
        $data['row'] = $this->userRepository->find($id);
        $data['page_title'] = trans('app.View'). ' '.trans('app.update password');
        $data['breadcrumb'] = [$this->title => route('school-account-manager.school-account-branches.get-update-password',$id)];
        return view($this->parent.'.'.$this->module.'.update_password',$data);
    }

    public function postUpdatePassword(SchoolAccountBranchUpdatePasswordsRequest $request)
    {
        $requestData = $request->all();
        $schoolAccountResult = $this->schoolAccountBranchUseCase->updatePassword($requestData, $request->user_id);
        flash()->success(trans('app.Update successfully'));
        return redirect()->route('school-account-manager.school-account-branches.get.index');

    }


    public function getUpdateUser(User $user)
    {
        $authUser = auth()->user();
        $userEnum = UserEnums::class;

        if ($authUser->type == UserEnums::SCHOOL_ACCOUNT_MANAGER) {
            $schoolAccountBranches = $this->repository
                ->getBranchesBySchoolAccountManagerPluck($authUser->id);
        }

        if ($user->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            $selectedBranches = $user->branches()->pluck("school_account_branches.id") ?? [];
        }


        $data['row'] = $this->userRepository->find($user->id);
        $data['page_title'] = trans('app.View'). ' '.trans('app.update password');
        $data['breadcrumb'] = [$this->title => route('school-account-manager.school-account-branches.get-update-password', $user->id)];
        $data['userEnum'] = $userEnum;
        $data['schoolAccountBranches'] = $schoolAccountBranches ?? [];
        $data['selectedBranches'] = $selectedBranches ?? [];

        return view($this->parent.'.'.$this->module.'.update', $data);
    }

    public function postUpdate(SchoolAccountBranchUpdateBranchesRequest $request)
    {
        $requestData = $request->all();
        $this->schoolAccountBranchUseCase->updateBranches($requestData, $request->user_id);
        flash()->success(trans('app.Update successfully'));
        return redirect()->route('school-account-manager.school-account-branches.get.index');

    }

    public function delete($id)
    {
        if($this->repository->delete($id)){
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('school-account-manager.school-account-branches.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getUsers()
    {
        $user = auth()->user();
        if($user->type == UserEnums::SCHOOL_ACCOUNT_MANAGER){
            $users = $this->repository->getSchoolUsers($user->schoolAccount);

            $data['rows'] = $users;
            $data['page_title'] = trans('app.users').' '.$this->title;
            $data['breadcrumb'] = [$this->title => route('school-account-manager.school-account-branches.get.users')];
            return view($this->parent.'.'.$this->module.'.users',$data);
        }
    }

    public function getSetRole($userId){
        $user = $this->userRepository->find($userId);
        $school_account_id = auth()->user()->schoolAccount->id;
        $data['row'] =$user ;
        $data['roles'] = $user->role->where('school_account_id' , $school_account_id)->get();
        $data['page_title'] = trans('app.View'). ' '.trans('app.set role');
        $data['breadcrumb'] = [$this->title => route('school-account-manager.school-account-branches.get-set-role',$userId)];
        return view($this->parent.'.'.$this->module.'.set-role',$data);
    }
    public function postSetRole(SetRoleRequest $request , $userId){
        $requestData = $request->all();
        $schoolAccountResult = $this->schoolAccountBranchUseCase->updateRole($requestData,$userId);
        return redirect()->route('school-account-manager.school-account-branches.get.users')->with(['success' => trans('app.Update successfully')]);
    }
}
