<?php

namespace App\OurEdu\Subjects\SME\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Countries\Repository\CountryRepositoryInterface;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;
use App\OurEdu\GradeClasses\Admin\Requests\GradeClassRequest;
use App\OurEdu\GradeClasses\Repository\GradeClassRepositoryInterface;

class SubjectStructureController extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $countryRepository;
    private $educationalSystemRepository;

    public function __construct(GradeClassRepositoryInterface $gradeClass, CountryRepositoryInterface $countryRepository, EducationalSystemRepositoryInterface $educationalSystemRepository)
    {
        $this->module = 'grade_classes';
        $this->repository = $gradeClass;
        $this->countryRepository = $countryRepository;
        $this->educationalSystemRepository = $educationalSystemRepository;

        $this->title = trans('grade_classes.Grade Class');
        $this->parent = ParentEnum::ADMIN;
    }

    public function getIndex()
    {
        $data['rows'] = $this->repository->jsonPaginate();
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
        $data['educationalSystemRepository'] = $this->educationalSystemRepository->pluck()->toArray();

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
}
