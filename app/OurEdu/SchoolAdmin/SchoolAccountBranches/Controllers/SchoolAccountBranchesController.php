<?php

namespace App\OurEdu\SchoolAdmin\SchoolAccountBranches\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAdmin\Middleware\SchoolAdminMiddleware;
use App\OurEdu\SchoolAdmin\SchoolAccountBranches\Repositories\SchoolBranchRepository;
use App\OurEdu\SchoolAdmin\SchoolAccountBranches\Requests\SchoolAccountBranchRequest;
use App\OurEdu\SchoolAdmin\SchoolAccountBranches\UseCases\SchoolAccountBranchUseCase;
use App\OurEdu\Users\Repository\SchoolAdminRepositoryInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\UserEnums;
use DB;
use Illuminate\Support\Facades\Auth;

class SchoolAccountBranchesController extends  BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $userRepository;

    public function __construct(
        // UserRepositoryInterface $userRepository,
    ) {
        $this->module = 'school_account_branches';
        $this->title = trans('app.School Account Branches');
        $this->repository = new SchoolBranchRepository();
        $this->parent = ParentEnum::SCHOOL_ADMIN;
        // $this->userRepository = $userRepository;
        $this->middleware(SchoolAdminMiddleware::class);
    }
    public function getIndex()
    {
        $data['rows'] = $this->repository->getBranchesByCurrentSchoolAccountId(
            auth()->user()->schoolAdmin->current_school_id
        )->paginate(5);

        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }


    public function getView($id)
    {
        $data['row'] = $this->repository->findWith($id,['supervisor','leader']);
        $data['page_title'] = trans('app.View').' '.$this->title;
        $data['breadcrumb'] = [$this->title => route('school-admin.school-account-branches.getView',$id)];
        return view($this->parent.'.'.$this->module.'.view',$data);
    }

    public function getEdit($id)
    {
        $branch = $this->repository->findWith($id,['schoolAccount','educationalSystems']);
        $data['row'] = $branch;
        $data['page_title'] = trans('app.Edit').' '.$this->title;
        $data['breadcrumb'] = [$this->title => route('admin.school-account-branches.get.index')];
        $data['educationalSystems'] = $this->repository->pluckBySchoolAccountId($branch->schoolAccount->id);
        return view($this->parent.'.'.$this->module.'.edit',$data);
    }


    public function putEdit(SchoolAccountBranchRequest $request,$id)
    {

        if (!$this->repository->getSchoolDefaultRole(Auth::user()->schoolAdmin->currentSchool->id)) {
            return redirect()->back()->with("error" , trans('you have to add at least one role firstly'));
        }
        $useCase =  new SchoolAccountBranchUseCase($this->repository);
        $requestData = $request->all();
        $schoolAccountResult = $useCase->update($requestData, $id);
        if ($schoolAccountResult) {
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('school-admin.school-account-branches.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }
}
