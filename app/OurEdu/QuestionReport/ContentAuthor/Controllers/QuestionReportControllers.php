<?php

namespace App\OurEdu\QuestionReport\ContentAuthor\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\QuestionReport\Models\QuestionReport;

class QuestionReportControllers extends BaseController
{
    private $module;
    private $model;
    private $title;
    private $parent;
    private $filters;
    use Filterable;



    public function __construct(QuestionReport $report)
    {
        $this->module = 'question_reports';
        $this->model = $report;

        $this->title = trans('reports.Report');
        $this->parent = ParentEnum::ADMIN;
    }

    public function getIndex()
    {
        $this->setFilters();
        $data['filters'] = $this->filters;
        $data['rows'] = $this->paginate($this->filters);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function paginate(array $filters = [])
    {
        $this->model = $this->applyFilters($this->model , $filters);
        return $this->model->orderBy('id','DESC')->paginate(15);
    }

    public function setFilters() {

        $this->filters[] = [
            'name' => 'is_reported',
            'type' => 'select',
            'data' => [
                0 => trans('reports.not reported'),
                1 => trans('reports.is_reported'),
            ],
            'value' => request()->get('is_reported'),
            'attributes' => [
                'id'=>'is_reported',
                'class'=>'form-control',
                'label'=>trans('reports.type'),
                'placeholder'=>trans('reports.type')
            ]
        ];
    }

}
