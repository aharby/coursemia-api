<?php

namespace App\OurEdu\Exams\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Countries\Repository\CountryRepositoryInterface;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;

class ExamController extends Controller
{
    private $title;
    private $module;
    private $repository;
    private $parent;
    protected $countryRepository;
    private $filters = [];

    public function __construct(
        ExamRepositoryInterface $ExamRepository,
        CountryRepositoryInterface $countryRepository
    ) {
        $this->module = 'exams';
        $this->title = trans('app.Exams');
        $this->repository = $ExamRepository;
        $this->parent = ParentEnum::ADMIN;
        $this->countryRepository = $countryRepository;
    }

    public function getStudentGrades($subjectId)
    {
        $this->setFilters();

        $data['module'] = $this->module;
        $data['page_title'] = trans('app.Student Grades');
        $data['breadcrumb'] = '';

        $data['rows'] = $this->repository->getStudentGrades($subjectId,[]);
        return view($this->parent . '.' . $this->module . '.studentGrades', $data);
    }

    public function setFilters()
    {
        $this->filters[] = [
            'name' => 'country_id',
            'type' => 'select',
            'data' => $this->countryRepository->pluck('id', 'name'), //optional if filter requires data ex: select type require list of choices to select from
            'value' => request()->get('country_id'),
            'attributes' => [
                'id'=>'country_id',
                'class'=>'form-control',
                'label'=>trans('exams.Country'),
                'placeholder'=>trans('exams.Country')
            ]
        ];

        $this->filters[] = [
            'name' => 'educational_system_id',
            'type' => 'select',
            'data' => [],
            'value' => request()->get('educational_system_id'),
            'attributes' => [
                'id'=>'educational_system_id',
                'class'=>'form-control',
                'label'=>trans('exams.Educational System'),
                'placeholder'=>trans('exams.Educational System')
            ]
        ];
    }
}
