<?php

namespace App\Modules\StaticPages\Controllers;

use App\Modules\BaseApp\Controllers\BaseController;
use App\Modules\BaseApp\Enums\ParentEnum;
use App\Modules\StaticBlocks\Repository\StaticBlocksRepositoryInterface;
use App\Modules\StaticPages\Repository\StaticPagesRepositoryInterface;
use App\Modules\StaticPages\Requests\StaticPageRequest;
use App\Modules\StaticPages\StaticPage;

class StaticPagesController extends BaseController
{
    private $module;
    private $staticPagesRepository;
    private $title;
    private $parent;


    public function __construct(StaticPagesRepositoryInterface $staticPagesRepository)
    {
        $this->module = 'staticPages';
        $this->title = trans('staticPage.Static Pages');
        $this->parent = ParentEnum::ADMIN;
        $this->staticPagesRepository = $staticPagesRepository;
    }

    public function getIndex()
    {
        $data['rows'] = $this->staticPagesRepository->paginate();
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getView($id)
    {
        $data['row'] = $this->staticPagesRepository->findOrFail($id);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.staticPages.get.index')];
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function getEdit($id)
    {
        $data['row'] = $this->staticPagesRepository->findOrFail($id);

        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.staticPages.get.index')];

        return view($this->parent . '.' . $this->module . '.edit', $data);
    }

    public function postEdit(StaticPageRequest $request , $id)
    {

        $update = $this->staticPagesRepository->update($id , $request->all());
        if ($update) {
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.staticPages.get.index');
        }

        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

}

