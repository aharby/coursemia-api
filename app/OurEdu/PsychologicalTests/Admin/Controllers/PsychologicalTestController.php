<?php

namespace App\OurEdu\PsychologicalTests\Admin\Controllers;

use Illuminate\Support\Facades\DB;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalTestRepository;
use App\OurEdu\PsychologicalTests\Admin\Requests\PsychologicalTestRequest;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalRecomendation;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalTestRepositoryInterface;

class PsychologicalTestController extends BaseController
{
    private $title;
    private $module;
    private $parent;
    private $repository;

    public function __construct(
        PsychologicalTestRepositoryInterface $psychoRepository
    ) {
        $this->module = 'psychological_tests';
        $this->repository = $psychoRepository;

        $this->title = trans('psychological_tests.Psychological Tests');
        $this->parent = ParentEnum::ADMIN;
    }

    public function getIndex()
    {
        $data['rows'] = $this->repository->paginate();
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.psychological_tests.get.index')];
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate()
    {
        $data['page_title'] = trans('app.Create') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.psychological_tests.get.index')];

        $data['row'] = new PsychologicalTest;

        return view($this->parent . '.' . $this->module . '.create', $data);
    }

    public function postCreate(PsychologicalTestRequest $request)
    {
        if ($course = $this->repository->create($request->all())) {
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.psychological_tests.get.index');
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->findOrFail($id);

        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.psychological_tests.get.index')];

        return view($this->parent . '.' . $this->module . '.edit', $data);
    }


    public function putEdit(PsychologicalTestRequest $request, $id)
    {
        $row = $this->repository->findOrFail($id);

        if ($this->repository->setPsychologicalTest($row)
            ->update($request->all())) {
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.psychological_tests.get.index');
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.psychological_tests.get.index')];

        $chartData = $data['row']->results()
            ->select(
                'psychological_recomendation_id as recomendation',
                DB::raw('count(id) as count')
            )
            ->groupBy('recomendation')->pluck('count', 'recomendation')->toArray();

        $data['chartLabels'] = PsychologicalRecomendation::orderBy('from')->find(array_keys($chartData))->each(function ($recomentation) {
            $recomentation->name = "{$recomentation->from} - {$recomentation->to}";
        })->pluck('name')->toArray();
        $data['chartValues'] = array_values($chartData);

        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function delete($id)
    {
        $row = $this->repository->findOrFail($id);

        if ($row->questions()->count()) {
            flash()->error(trans('app.You cant delete a test contains questios'));
            return redirect()->back();
        }

        if ($row->options()->count()) {
            flash()->error(trans('app.You cant delete a test contains options'));
            return redirect()->back();
        }

        if ($row->recomendations()->count()) {
            flash()->error(trans('app.You cant delete a test contains recomendation'));
            return redirect()->back();
        }

        $rep = new PsychologicalTestRepository($row);
        if ($rep->delete()) {
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.psychological_tests.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }
}
