<?php


namespace App\OurEdu\Countries\Admin\Controllers;

use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\Countries\Admin\Requests\CountryRequest;
use App\OurEdu\Countries\Repository\CountryRepositoryInterface;
use App\OurEdu\Countries\Middleware\CheckCountryUsageMiddleware;

class CountriesController extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    public function __construct(CountryRepositoryInterface $interface)
    {
        $this->module = 'countries';
        $this->title = trans('app.Countries');
        $this->repository = $interface;
        $this->parent = ParentEnum::ADMIN;
        $this->middleware(CheckCountryUsageMiddleware::class)->only(['delete']);
    }
    public function getIndex()
    {
        $data['rows'] = $this->repository->all();
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate()
    {
        $data['page_title'] = trans('app.Create').' '.$this->title;
        $data['row'] = $this->repository;
        $data['breadcrumb'] = [$this->title => route('admin.countries.get.index')];
        return view($this->parent . '.' . $this->module . '.create', $data);
    }

    public function postCreate(CountryRequest $request)
    {
        if ($this->repository->create($request->all())) {
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.countries.get.index');
        } else {
            flash()->error(trans('app.Oopps Something is broken'));
            return redirect()->back();
        }
    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->find($id);
        $data['page_title'] = trans('app.Edit').' '.$this->title;
        $data['breadcrumb'] = [$this->title => route('admin.countries.get.index')];
        return view($this->parent.'.'.$this->module.'.edit',$data);
    }


    public function putEdit(CountryRequest $request,$id)
    {
        if ($this->repository->update($id,$request->all())){
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.countries.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->find($id);
        $data['page_title'] = trans('app.View').' '.$this->title;
        $data['breadcrumb'] = [$this->title => route('admin.countries.get.index')];
        return view($this->parent.'.'.$this->module.'.view',$data);
    }

    public function delete($id)
    {
        if($this->repository->delete($id)){
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.countries.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

}
