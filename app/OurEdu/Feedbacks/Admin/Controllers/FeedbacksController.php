<?php

namespace App\OurEdu\Feedbacks\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Feedbacks\Repository\FeedbackRepositoryInterface;


class FeedbacksController extends Controller
{
    private $title;
    private $module;
    private $repository;
    private $parent;


    public function __construct(
        FeedbackRepositoryInterface $feedbackRepository
    ) {
        $this->module = 'feedbacks';
        $this->title = trans('app.Feedbacks');
        $this->repository = $feedbackRepository;
        $this->parent = ParentEnum::ADMIN;
    }

    public function getIndex()
    {
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List Feedbacks');
        $data['breadcrumb'] = '';
        $data['rows'] = $this->repository->all();
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function approve($id)
    {
        $feedback = $this->repository->findOrFail($id);
        if($feedback->approved == 0) {
            $feedback = $this->repository->update($feedback, ['approved' => 1]);
            flash(trans('app.Approved Successfully'))->success();
        } else {
            flash(trans('feedbacks.Already Approved'))->message();
        }
        return redirect()->route('admin.feedbacks.get.index');
    }

    public function delete($id)
    {
        $feedback = $this->repository->findOrFail($id);
        $this->repository->delete($feedback);
        flash()->success(trans('app.Deleted Successfully'));
        return redirect()->route('admin.feedbacks.get.index');
    }

}
