<?php


namespace App\OurEdu\Options\Admin\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Options\Admin\Requests\OptionRequest;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Repository\OptionRepositoryInterface;

class OptionsController extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;

    public function __construct(OptionRepositoryInterface $optionRepository)
    {
        $this->module = 'options';
        $this->repository = $optionRepository;
        $this->title = trans('options.Options');
        $this->parent = ParentEnum::ADMIN;
    }

    public function getIndex()
    {
        $data['rows'] = $this->repository->paginate(12);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate()
    {
        $data['page_title'] = trans('app.Create') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.options.get.index')];
        $data['optionTypes'] = OptionsTypes::getOptionsTypes();
        return view($this->parent . '.' . $this->module . '.create', $data);
    }

    public function postCreate(OptionRequest $request)
    {

        $data = $request->except('_token');
        if ($this->repository->create($data)) {
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.options.get.index');
        } else {
            flash()->error(trans('app.Oopps Something is broken'));
            return redirect()->back();
        }
    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.options.get.index')];
        $data['optionTypes'] = OptionsTypes::getOptionsTypes();
        return view($this->parent . '.' . $this->module . '.edit', $data);
    }


    public function putEdit(OptionRequest $request, $id)
    {
        $row = $this->repository->findOrFail($id);
        if ($this->repository->update($row, $request->all())) {
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.options.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.options.get.index')];
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function delete($id)
    {
        $row = $this->repository->findOrFail($id);
        if ($this->repository->delete($row)) {
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.options.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }
}
