<?php


namespace App\OurEdu\AcademicYears\Admin\Controllers;


use App\OurEdu\AcademicYears\Repository\AcademicYearRepositoryInterface;
use App\OurEdu\AcademicYears\Admin\Requests\AcademicYearRequest;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Countries\Repository\CountryRepositoryInterface;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;

class AcademicYearsController extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $countries;
    private $educationalSystem;


    public function __construct(
        AcademicYearRepositoryInterface $interface,
        CountryRepositoryInterface $countryRepository ,
        EducationalSystemRepositoryInterface $educationalSystemRepository
    )
    {
        $this->module = 'academicYears';
        $this->title = trans('app.Academic Years');
        $this->repository = $interface;
        $this->parent = ParentEnum::ADMIN;
        $this->countries = $countryRepository->all()->pluck('name' , 'id')->toArray();
        $this->educationalSystem = $educationalSystemRepository;
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
        $data['breadcrumb'] = [$this->title=>route('admin.academicYears.get.index')];
        $data['row'] = $this->repository;
        $data['countries'] = $this->countries;
        $data['educationalSystem'] = $this->educationalSystem->pluck();
        return view($this->parent . '.' . $this->module . '.create', $data);
    }

    public function postCreate(AcademicYearRequest $request)
    {
        if ($this->repository->create($request->all())) {
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.academicYears.get.index');
        } else {
            flash()->error(trans('app.Oopps Something is broken'));
            return redirect()->back();
        }
    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->find($id);
        $data['page_title'] = trans('app.Edit').' '.$this->title;
        $data['breadcrumb'] = [$this->title=>route('admin.academicYears.get.index')];
        $data['countries'] = $this->countries;
        $data['educationalSystem'] = $this->educationalSystem->pluckByCountryId($data['row']->country_id);
        return view($this->parent.'.'.$this->module.'.edit',$data);
    }


    public function putEdit(AcademicYearRequest $request,$id)
    {
        if ($this->repository->update($id,$request->all())){
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.academicYears.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->find($id);
        $data['page_title'] = trans('app.View').' '.$this->title;
        $data['breadcrumb'] = [$this->title=>route('admin.academicYears.get.index')];
        return view($this->parent.'.'.$this->module.'.view',$data);
    }

    public function delete($id)
    {
        if($this->repository->delete($id)){
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.academicYears.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getEducationalSystem(){
        if ($countryId = request('country_id')) {
            $educationalSystem = $this->educationalSystem->pluckByCountryId($countryId);

            return response()->json(
                [
                    'status' => '200',
                    'educationSystem' => $educationalSystem
                ]
            );
        }
    }

}
