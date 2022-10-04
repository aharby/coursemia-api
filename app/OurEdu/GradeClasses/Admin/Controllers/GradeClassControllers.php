<?php

namespace App\OurEdu\GradeClasses\Admin\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Countries\Repository\CountryRepositoryInterface;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;
use App\OurEdu\GradeClasses\Admin\Requests\GradeClassRequest;
use App\OurEdu\GradeClasses\Middleware\CheckGradeClassesUsageMiddleware;
use App\OurEdu\GradeClasses\Repository\GradeClassRepositoryInterface;

class GradeClassControllers extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $countryRepository;
    private $educationalSystemRepository;
    private $filters;

    public function __construct(GradeClassRepositoryInterface $gradeClass, CountryRepositoryInterface $countryRepository, EducationalSystemRepositoryInterface $educationalSystemRepository)
    {
        $this->module = 'grade_classes';
        $this->repository = $gradeClass;
        $this->countryRepository = $countryRepository;
        $this->educationalSystemRepository = $educationalSystemRepository;

        $this->title = trans('grade_classes.Grade Class');
        $this->parent = ParentEnum::ADMIN;
        $this->middleware(CheckGradeClassesUsageMiddleware::class)->only('delete');
    }

    public function getIndex()
    {
        $this->setFilters();
        $data['filters'] = $this->filters;
        $data['rows'] = $this->repository->paginate($this->filters);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate()
    {
        $data['page_title'] = trans('app.Create') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.gradeClasses.get.index')];

        $data['countries'] = $this->countryRepository->pluck()->toArray();
        $data['educationalSystemRepository'] = $this->educationalSystemRepository->pluck()->toArray();

        return view($this->parent . '.' . $this->module . '.create', $data);
    }

    public function postCreate(GradeClassRequest $request)
    {

        $data = $request->except('_token');
//        dd($data);
        if ($this->repository->create($data)) {
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.gradeClasses.get.index');
        } else {
            flash()->error(trans('app.Oopps Something is broken'));
            return redirect()->back();
        }
    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.gradeClasses.get.index')];

        $data['countries'] = $this->countryRepository->pluck()->toArray();
        $data['educationalSystemRepository'] = $this->educationalSystemRepository->pluckByCountryId($data['row']->country_id);
        return view($this->parent . '.' . $this->module . '.edit', $data);
    }


    public function putEdit(GradeClassRequest $request, $id)
    {
        $row = $this->repository->findOrFail($id);
        if ($this->repository->update($row, $request->all())) {
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.gradeClasses.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.gradeClasses.get.index')];
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function delete($id)
    {
        $row = $this->repository->findOrFail($id);
        if ($this->repository->delete($row)) {
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.gradeClasses.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getEducationalSystem(){
        if ($countryId = request('country_id')) {
            $educationalSystem = $this->educationalSystemRepository->pluckByCountryId($countryId);

            return response()->json(
                [
                    'status' => '200',
                    'educationSystem' => $educationalSystem
                ]
            );
        }
    }

    public function setFilters() {
        $this->filters[] = [
            'name' => 'title',
            'type' => 'input',
            'trans' => true,
            'value' => request()->get('title' ),
            'attributes' => [
                'class'=>'form-control',
                'label'=>trans('grade_classes.title'),
                'placeholder'=>trans('grade_classes.title'),
            ]
        ];

        $this->filters[] = [
            'name' => 'country_id',
            'type' => 'select',
            'data' => $this->countryRepository->pluck('id', 'name'), //optional if filter requires data ex: select type require list of choices to select from
            'value' => request()->get('country_id'),
            'attributes' => [
                'id'=>'country_id',
                'class'=>'form-control',
                'label'=>trans('grade_classes.Country'),
                'placeholder'=>trans('grade_classes.Country')
            ]
        ];
        $this->filters[] = [
            'name' => 'educational_system_id',
            'type' => 'select',
            'data' => $this->educationalSystemRepository->pluck('id' , 'name'), //optional if filter requires data ex: select type require list of choices to select from
            'value' => request()->get('educational_system_id'),
            'attributes' => [
                'id'=>'country_id',
                'class'=>'form-control',
                'label'=>trans('grade_classes.Educational System'),
                'placeholder'=>trans('grade_classes.Educational System')
            ]
        ];
        $this->filters[] = [
            'name' => 'is_active',
            'type' => 'select',
            'data' => [
                0 => trans('grade_classes.not active'),
                1 => trans('grade_classes.active'),
            ], //optional if filter requires data ex: select type require list of choices to select from
            'value' => request()->get('is_active'),
            'attributes' => [
                'id'=>'is_active',
                'class'=>'form-control',
                'label'=>trans('grade_classes.is_active'),
                'placeholder'=>trans('grade_classes.is_active')
            ]
        ];
    }

}
