<?php

namespace App\OurEdu\Reports\Admin\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Reports\ReportEnum;
use App\OurEdu\Reports\Repository\ReportRepositoryInterface;

class ReportControllers extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $filters = [];



    public function __construct(ReportRepositoryInterface $report)
    {
        $this->module = 'reports';
        $this->repository = $report;

        $this->title = trans('reports.Report');
        $this->parent = ParentEnum::ADMIN;
    }

    public function getIndex()
    {
        $this->setFilters();
        $data['filters'] = $this->filters;
        $data['rows'] = $this->repository->all($this->filters);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getDetails($id)
    {
        $data['module'] = $this->module;
        $data['page_title'] =  trans('reports.report details');
        $data['breadcrumb'] = [$this->title => route('admin.reports.get.index')];
        $data['row'] = $this->repository->findOrFail($id);
        return view($this->parent . '.' . $this->module . '.reportDetails', $data);
    }

    public function setFilters()
    {
        $this->filters[] = [
            'name' => 'reportable_type',
            'type' => 'select',
            'data' => ReportEnum::availableTypes(),
            'value' => request()->get('reportable_type'),
            'attributes' => [
                'class'=>'form-control',
                'label'=>trans('reports.reportable_type'),
                'placeholder'=>trans('reports.reportable_type')
            ]
        ];
    }


}
