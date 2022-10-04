<?php

namespace App\OurEdu\PsychologicalTests\Admin\Controllers;

use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalAnswer;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalQuestion;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalQuestionRepository;
use App\OurEdu\PsychologicalTests\Admin\Requests\PsychologicalQuestionRequest;
use App\OurEdu\PsychologicalTests\Repository\PsychologicalQuestionRepositoryInterface;

class PsychologicalQuestionController extends BaseController
{
    private $title;
    private $module;
    private $parent;
    private $repository;

    public function __construct(
        PsychologicalQuestionRepositoryInterface $psychoRepository
    ) {
        $this->module = 'psychological_questions';
        $this->repository = $psychoRepository;

        $this->title = trans('psychological_questions.Psychological Questions');
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
        $data['breadcrumb'] = [$this->title => route('admin.psychological_questions.get.index', $testId)];

        $data['row'] = new PsychologicalQuestion;
        $data['testId'] = $testId;

        return view($this->parent . '.' . $this->module . '.create', $data);
    }


    public function postCreate($testId, PsychologicalQuestionRequest $request)
    {
        if ($course = $this->repository->create($testId, $request->all())) {
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.psychological_questions.get.index', $testId);
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->findOrFail($id);

        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.psychological_questions.get.index', $data['row']->psychological_test_id)];

        return view($this->parent . '.' . $this->module . '.edit', $data);
    }


    public function putEdit(PsychologicalQuestionRequest $request, $id)
    {
        $row = $this->repository->findOrFail($id);

        if ($this->repository->setPsychologicalQuestion($row)
            ->update($request->all())) {
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.psychological_questions.get.index', $row->psychological_test_id);
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->findOrFail($id);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.psychological_questions.get.index', $data['row']->psychological_test_id)];
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function delete($id)
    {
        $row = $this->repository->findOrFail($id);

        if (PsychologicalAnswer::where('psychological_question_id', $id)->exists()) {
            flash()->error(trans('app.This row is related to actual answers'));
            return redirect()->back();
        }

        $rep = new PsychologicalQuestionRepository($row);
        if ($rep->delete()) {
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.psychological_questions.get.index', $row->psychological_test_id);
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }
}
