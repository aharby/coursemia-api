<?php


namespace App\OurEdu\Roles\SchoolManger\Controller;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Roles\Repository\RoleRepositoryInterface;
use App\OurEdu\Roles\Role;
use App\OurEdu\Roles\SchoolManger\Requests\RoleRequest;

class RolesController extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $model;

    public function __construct(RoleRepositoryInterface $roleRepository , Role $role)
    {
        $this->module = 'roles';
        $this->repository = $roleRepository;
        $this->title = trans('roles.Roles');
        $this->parent = ParentEnum::SCHOOL_ACCOUNT_MANAGER;
        $this->model = $role;
    }

    public function getIndex()
    {
        $data['rows'] = $this->repository->paginate(12);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        $data['module'] = $this->module;
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate()
    {
        $data['page_title'] = trans('app.Create') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route( 'school-account-manager.roles.index')];
        $data['row'] = $this->model;
        return view($this->parent . '.' . $this->module . '.create', $data);
    }

    public function postCreate(RoleRequest $request)
    {
        $data = $request->except('_token');
        $school_account_id = auth()->user()->schoolAccount->id ;
        $data['school_account_id'] = $school_account_id;
        if ($this->repository->create($data)) {
            flash()->success(trans('app.Created successfully'));
            return redirect()->route( 'school-account-manager.roles.index');
        } else {
            flash()->error(trans('app.Oopps Something is broken'));
            return redirect()->back();
        }
    }

    public function getEdit($roleId)
    {
        $data['row'] = $this->repository->findOrFail($roleId);
        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('school-account-manager.roles.index')];
        return view($this->parent . '.' . $this->module . '.edit', $data);
    }


    public function postEdit(RoleRequest $request, $roleId)
    {
        $row = $this->repository->findOrFail($roleId);
        if ($this->repository->update($row, $request->all())) {
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('school-account-manager.roles.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

}
