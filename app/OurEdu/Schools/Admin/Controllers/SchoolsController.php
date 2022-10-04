<?php

namespace App\OurEdu\Schools\Admin\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Countries\Repository\CountryRepositoryInterface;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;
use App\OurEdu\Schools\Admin\Requests\SchoolRequest;
use App\OurEdu\Schools\Middleware\CheckSchoolUsageMiddleware;
use App\OurEdu\Schools\Repository\SchoolRepositoryInterface;

class SchoolsController extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $countryRepository;
    private $educationalSystemRepository;
    private $filters = [];

    public function __construct(
        SchoolRepositoryInterface $schoolRepository,
        CountryRepositoryInterface $countryRepository,
        EducationalSystemRepositoryInterface $educationalSystemRepository
    ) {
        $this->module = 'schools';
        $this->repository = $schoolRepository;
        $this->countryRepository = $countryRepository;
        $this->educationalSystemRepository = $educationalSystemRepository;

        $this->title = trans('schools.Schools');
        $this->parent = ParentEnum::ADMIN;
        $this->middleware(CheckSchoolUsageMiddleware::class)->only('delete');
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
        $data['breadcrumb'] = [$this->title => route('admin.schools.get.index')];

        $data['countries'] = $this->countryRepository->pluck()->toArray();
        $data['educationalSystemRepository'] = $this->educationalSystemRepository->pluck()->toArray();

        return view($this->parent . '.' . $this->module . '.create', $data);
    }

    public function postCreate(SchoolRequest $request)
    {

        $data = $request->except('_token');
        if ($this->repository->create($data)) {
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.schools.get.index');
        } else {
            flash()->error(trans('app.Oopps Something is broken'));
            return redirect()->back();
        }
    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.schools.get.index')];

        $data['countries'] = $this->countryRepository->pluck()->toArray();
        $data['educationalSystemRepository'] = $this->educationalSystemRepository->pluckByCountryId($data['row']->country_id);
        return view($this->parent . '.' . $this->module . '.edit', $data);
    }


    public function putEdit(SchoolRequest $request, $id)
    {
        $row = $this->repository->findOrFail($id);
        if ($this->repository->update($row, $request->all())) {
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.schools.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.schools.get.index')];
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function delete($id)
    {
        $row = $this->repository->findOrFail($id);
        if ($this->repository->delete($row)) {
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.schools.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getEducationalSystem()
    {
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
            'name' => 'name',
            'type' => 'input',
            'trans' => true,
            'value' => request()->get('name' ),
            'attributes' => [
                'class'=>'form-control',
                'label'=>trans('schools.name'),
                'placeholder'=>trans('schools.name'),
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
                'label'=>trans('schools.Country'),
                'placeholder'=>trans('schools.Country')
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
                'label'=>trans('schools.Educational System'),
                'placeholder'=>trans('schools.Educational System')
            ]
        ];
    }

}
