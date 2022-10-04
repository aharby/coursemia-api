<?php

namespace App\OurEdu\PsychologicalTests\Admin\Controllers;

use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalAnswer;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalOption;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalOptionRepository;
use App\OurEdu\PsychologicalTests\Admin\Requests\PsychologicalOptionRequest;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalOptionRepositoryInterface;

class PsychologicalOptionController extends BaseController
{
    private $title;
    private $module;
    private $parent;
    private $repository;

    public function __construct(
        PsychologicalOptionRepositoryInterface $psychoRepository
    ) {
        $this->module = 'psychological_options';
        $this->repository = $psychoRepository;

        $this->title = trans('psychological_options.Psychological Options');
        $this->parent = ParentEnum::ADMIN;
    }

    public function getIndex($testId)
    {
        $data['rows'] = $this->repository->paginate($testId);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = [trans('psychological_tests.Psychological Tests') => route('admin.psychological_tests.get.index')];
        $data['testId'] = $testId;
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate($testId)
    {
        $data['page_title'] = trans('app.Create') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.psychological_options.get.index', $testId)];

        $data['row'] = new PsychologicalOption;
        $data['testId'] = $testId;

        return view($this->parent . '.' . $this->module . '.create', $data);
    }


    public function postCreate($testId, PsychologicalOptionRequest $request)
    {
        if ($course = $this->repository->create($testId, $request->all())) {
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.psychological_options.get.index', $testId);
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->findOrFail($id);

        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.psychological_options.get.index', $data['row']->psychological_test_id)];

        return view($this->parent . '.' . $this->module . '.edit', $data);
    }


    public function putEdit(PsychologicalOptionRequest $request, $id)
    {
        $row = $this->repository->findOrFail($id);

        if ($this->repository->setPsychologicalOption($row)
            ->update($request->all())) {
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.psychological_options.get.index', $row->psychological_test_id);
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.psychological_options.get.index', $data['row']->psychological_test_id)];
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function delete($id)
    {
        $row = $this->repository->findOrFail($id);

        if (PsychologicalAnswer::where('psychological_option_id', $id)->exists()) {
            flash()->error(trans('app.This row is related to actual answers'));
            return redirect()->back();
        }

        $rep = new PsychologicalOptionRepository($row);
        if ($rep->delete()) {
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.psychological_options.get.index', $row->psychological_test_id);
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }
}
