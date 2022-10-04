<?php


namespace App\OurEdu\Instructors\Admin\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Instructors\Admin\Exports\InstructorsExport;
use App\OurEdu\Users\Repository\InstructorRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;

class InstructorsController extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $filters = [];



    public function __construct(InstructorRepositoryInterface $instructorRepository)
    {
        $this->module = 'instructors';
        $this->repository = $instructorRepository;
        $this->title = trans('instructors.Instructors');
        $this->parent = ParentEnum::ADMIN;
    }

    public function getIndex()
    {

        $this->setFilters();
        $data['filters'] = $this->filters;
        $data['rows'] = $this->repository->all($this->filters);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        $data['module'] = $this->module;
        return view($this->parent . '.' . $this->module . '.index', $data);
    }
    public function getView($id)
    {
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.View') . " " . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.instructors.get.index')];
        $data['row'] = $this->repository->findOrFail($id);
        $data['students_count'] = $this->repository->studentCoursesCountNumber($id);
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function getDetails($id)
    {
        $data['module'] = $this->module;
        $data['page_title'] =  trans('instructors.rating details');
        $data['breadcrumb'] = [$this->title => route('admin.instructors.get.index')];
        $data['row'] = $this->repository->findOrFail($id);
        return view($this->parent . '.' . $this->module . '.ratingDetails', $data);
    }


    public function setFilters()
    {
        $this->filters[] = [
            'name' => 'user_id',
            'type' => 'relation',
            'key' => 'user_id',
            'relation' => 'User',
            'data' => $this->repository->pluck()->toArray(),
            'trans' => false,
            'value' => request()->get('user_id'),
            'attributes' => [
                'class' => 'form-control',
                'label' => trans('instructors.Instructor'),
                'placeholder' => trans('instructors.Instructor'),
            ]
        ];
    }

    public function Export()
    {
       $this->setFilters();
       $data = $this->repository->export($this->filters);

        return Excel::download(new InstructorsExport($data), "List-instructors.xls");
    }


}
