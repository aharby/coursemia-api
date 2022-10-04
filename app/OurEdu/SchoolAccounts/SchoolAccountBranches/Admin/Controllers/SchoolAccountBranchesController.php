<?php
namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\Admin\Controllers;

use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\SchoolAccounts\Middleware\SchoolAccountBranchAdminMiddleware;
use App\OurEdu\SchoolAccounts\SchoolAccounts\Repository\SchoolAccountRepository;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Admin\Requests\SchoolAccountBranchRequest;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;

class SchoolAccountBranchesController extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $parentPermession;

    private $schoolAccountRepository;

    public function __construct(
        SchoolAccountBranchesRepository $schoolAccountBranchesRepository,
        SchoolAccountRepository $schoolAccountRepository
    )
    {
        $this->module = trans('school_account_branches');
        $this->title = trans('app.School Account Branches');
        $this->repository = $schoolAccountBranchesRepository;
        $this->schoolAccountRepository = $schoolAccountRepository;
        $this->parent = ParentEnum::ADMIN;
        $this->parentPermession = ParentEnum::SCHOOL_SUPERVISOR;

        $this->middleware(SchoolAccountBranchAdminMiddleware::class);

    }
    public function getIndex()
    {
        $data['rows'] = $this->repository->allWith(['schoolAccount']);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate()
    {
        $data['page_title'] = trans('app.Create').' '.$this->title;
        $data['breadcrumb'] = [$this->title => route($this->parent.'.school-account-branches.get.create')];
        $data['schoolAccounts'] = $this->schoolAccountRepository->pluck();
        $data['meetingTypes'] = VCRProvidersEnum::getList();
        return view($this->parent . '.' . $this->module . '.create', $data);
    }

    public function postCreate(SchoolAccountBranchRequest $request)
    {
        $data = $request->except('_token');
        if ($schoolAccount = $this->repository->create($data)) {
            flash()->success(trans('app.Created successfully'));
            return redirect()->route($this->parent.'.school-account-branches.get.index');
        } else {
            flash()->error(trans('app.Oopps Something is broken'));
            return redirect()->back();
        }

    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->findWith($id,['schoolAccount']);
        $data['page_title'] = trans('app.Edit').' '.$this->title;
        $data['breadcrumb'] = [$this->title => route($this->parent.'.school-account-branches.get.edit',$id)];
        $data['schoolAccounts'] = $this->schoolAccountRepository->pluck();
        $data['meetingTypes'] = VCRProvidersEnum::getList();

        return view($this->parent.'.'.$this->module.'.edit',$data);
    }


    public function putEdit(SchoolAccountBranchRequest $request,$id)
    {
        if ($this->repository->update($id,$request->all())){
            $branch = $this->repository->find($id);
            $branchRepo = new SchoolAccountBranchesRepository($branch);

            if (is_array($request->educational_systems) && count($request->educational_systems)) {
                $branchRepo->attachEducationalSystems($request->educational_systems);
            }

            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.school-account-branches.get.index');
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->findWith($id,['supervisor','leader']);
        $data['page_title'] = trans('app.View').' '.$this->title;
        $data['breadcrumb'] = [$this->title => route('admin.school-account-branches.get.index')];
        return view($this->parent.'.'.$this->module.'.view',$data);
    }

    public function delete($id)
    {
        if($this->repository->delete($id)){
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.school-account-branches.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

}
