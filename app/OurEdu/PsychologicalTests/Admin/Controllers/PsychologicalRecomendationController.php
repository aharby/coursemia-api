<?php

namespace App\OurEdu\PsychologicalTests\Admin\Controllers;

use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalResult;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalRecomendation;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalRecomendationRepository;
use App\OurEdu\PsychologicalTests\Admin\Requests\PsychologicalRecomendationRequest;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalRecomendationRepositoryInterface;

class PsychologicalRecomendationController extends BaseController
{
    private $title;
    private $module;
    private $parent;
    private $repository;

    public function __construct(
        PsychologicalRecomendationRepositoryInterface $psychoRepository
    ) {
        $this->module = 'psychological_recomendations';
        $this->repository = $psychoRepository;

        $this->title = trans('psychological_recomendations.Psychological Recomendations');
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
        $data['breadcrumb'] = [$this->title => route('admin.psychological_recomendations.get.index', $testId)];

        $data['row'] = new PsychologicalRecomendation;
        $data['testId'] = $testId;

        return view($this->parent . '.' . $this->module . '.create', $data);
    }


    public function postCreate($testId, PsychologicalRecomendationRequest $request)
    {
        if ($course = $this->repository->create($testId, $request->all())) {
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.psychological_recomendations.get.index', $testId);
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->findOrFail($id);

        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.psychological_recomendations.get.index', $data['row']->psychological_test_id)];

        return view($this->parent . '.' . $this->module . '.edit', $data);
    }


    public function putEdit(PsychologicalRecomendationRequest $request, $id)
    {
        $row = $this->repository->findOrFail($id);

        if ($this->repository->setPsychologicalRecomendation($row)
            ->update($request->all())) {
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.psychological_recomendations.get.index', $row->psychological_test_id);
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.psychological_recomendations.get.index', $data['row']->psychological_test_id)];
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function delete($id)
    {
        $row = $this->repository->findOrFail($id);

        if (PsychologicalResult::where('psychological_recomendation_id', $id)->exists()) {
            flash()->error(trans('app.This row is related to actual results'));
            return redirect()->back();
        }

        $rep = new PsychologicalRecomendationRepository($row);
        if ($rep->delete()) {
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.psychological_recomendations.get.index', $row->psychological_test_id);
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }
}
