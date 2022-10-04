<?php


namespace App\OurEdu\EducationalSystems\Admin\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Countries\Repository\CountryRepositoryInterface;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\EducationalSystems\Middleware\CheckEducationalSystemUsageMiddleware;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;
use App\OurEdu\EducationalSystems\Admin\Requests\EducationalSystemRequest;
use App\Producers\EducationalSystem\EducationalSystemUpdated;

class EducationalSystemsController extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $countries;
    private $filters = [];

    public function __construct(EducationalSystemRepositoryInterface $interface , CountryRepositoryInterface $countryRepository)
    {
        $this->repository = $interface;
        $this->module = 'educationalSystems';
        $this->title = trans('app.Educational Systems');
        $this->parent = ParentEnum::ADMIN;
        $this->countries = $countryRepository->all()->where('is_active',1)->pluck('name' , 'id')->toArray();
        $this->middleware(CheckEducationalSystemUsageMiddleware::class)->only('delete');
    }
    public function getIndex()
    {
        $this->setFilters();
        $data['filters'] = $this->filters;
        $data['rows'] = $this->repository->all($this->filters);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate()
    {
        $data['page_title'] = trans('app.Create').' '.$this->title;
        $data['row'] = $this->repository;
        $data['countries'] = $this->countries;
        $data['breadcrumb'] = [$this->title => route('admin.educationalSystems.get.index')];
        return view($this->parent . '.' . $this->module . '.create', $data);
    }

    public function postCreate(EducationalSystemRequest $request)
    {
        if ($this->repository->create($request->all())) {
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.educationalSystems.get.index');
        } else {
            flash()->error(trans('app.Oopps Something is broken'));
            return redirect()->back();
        }
    }

    public function getEdit(EducationalSystem $educationalSystem)
    {
        $data['row'] = $educationalSystem;
        $data['page_title'] = trans('app.Edit').' '.$this->title;
        $data['countries'] = $this->countries;
        $data['breadcrumb'] = [$this->title => route('admin.educationalSystems.get.index')];
        return view($this->parent.'.'.$this->module.'.edit',$data);
    }


    public function putEdit(EducationalSystemRequest $request,EducationalSystem $educationalSystem)
    {
        if ($this->repository->update($educationalSystem->id,$request->all())){
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.educationalSystems.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->find($id);
        $data['page_title'] = trans('app.View').' '.$this->title;
        $data['breadcrumb'] = [$this->title => route('admin.educationalSystems.get.index')];
        return view($this->parent.'.'.$this->module.'.view',$data);
    }

    public function delete($id)
    {
        if($this->repository->delete($id)){
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.educationalSystems.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function setFilters() {
        $this->filters[] = [
            'name' => 'name',
            'type' => 'input',
            'trans' => true,
            'value' => request()->get('name' ),
            'attributes' => [
                'class'=>'form-control',
                'label'=>trans('educational_systems.name'),
                'placeholder'=>trans('educational_systems.name'),
            ]
        ];
        $this->filters[] = [
            'name' => 'country_id',
            'type' => 'select',
            'data' => $this->countries, //optional if filter requires data ex: select type require list of choices to select from
            'value' => request()->get('country_id'),
            'attributes' => [
                'id'=>'country_id',
                'class'=>'form-control',
                'label'=>trans('educational_systems.Country'),
                'placeholder'=>trans('educational_systems.Country')
            ]
        ];
    }

}
