<?php

namespace App\OurEdu\Subjects\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Option;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectLogsRepositoryInterface;
use App\OurEdu\Users\User;

class SubjectLogsController extends Controller
{
    private $title;
    private $module;
    private $model;
    private $subjectRepository;
    private $logsRepository;
    private $parent;
    private $filters;
    use Filterable;

    public function __construct(
        SubjectRepositoryInterface $subjectRepository,
        SubjectLogsRepositoryInterface $logsRepository,
        SubjectFormatSubject $subject
    ) {
        $this->model = $subject;
        $this->module = 'subjects';
        $this->title = trans('app.Subjects');
        $this->subjectRepository = $subjectRepository;
        $this->logsRepository = $logsRepository;
        $this->parent = ParentEnum::ADMIN;
    }

    public function listSubjectLogs($id)
    {
        $data['rows'] = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',[Subject::class,'subject'])
            ->where('auditable_id',$id)
            ->orderBy('id','DESC')->paginate(50);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List Subject Logs');
        $data['breadcrumb'] = [trans('navigation.Subjects') => route('admin.subjects.get.index')];
        return view($this->parent . '.' . $this->module . '.logsIndex', $data);
    }

    public function listSubjectStructreLogs()
    {
        $this->setFilters();
        $data['filters'] = $this->filters;
        $data['rows'] = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',[SubjectFormatSubject::class,'subjectFormatSubject'])->paginate(15);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.List Subject Structure Logs');
        return view($this->parent . '.' . 'subjectFormatSubject' . '.logsIndex', $data);
    }

    public function paginate(array $filters = [])
    {
        $this->model = $this->applyFilters($this->model , $filters);
        return $this->model->orderBy('id','DESC')->paginate(15);
    }

    public function setFilters()
    {
        $this->filters[] = [
            'name' => 'title',
            'type' => 'input',
            'trans' => false,
            'value' => request()->get('title'),
            'attributes' => [
                'class' => 'form-control',
                'label' => trans('subject.title'),
                'placeholder' => trans('subject.title'),
            ]
        ];

    }

    public function viewSubjectLog($id)
    {
        $data['row'] = $this->logsRepository->findOrFail($id);
        $data['subject'] = $data['row']->subject;
        $data['before']['country_name'] = Country::find($data['subject']['country_id'])->name??'';
        $data['after']['country_name'] = Country::find($data['row']->event_properties['subjectAttributes']['country_id'])->name??'';
        $data['before']['educational_name'] = EducationalSystem::find($data['subject']['educational_system_id'])->name??'';
        $data['after']['educational_name'] = EducationalSystem::find($data['row']->event_properties['subjectAttributes']['educational_system_id'])->name??'';
        $data['before']['educational_term'] = Option::find($data['subject']['educational_term_id'])->title??'';
        $data['after']['educational_term'] = Option::find($data['row']->event_properties['subjectAttributes']['educational_term_id'])->title??'';
        $data['before']['academical_years'] = Option::find($data['subject']['academical_years_id'])->title??'';
        $data['after']['academical_years'] = Option::find($data['row']->event_properties['subjectAttributes']['academical_years_id'])->title??'';
        $data['before']['sme'] = User::find($data['subject']['sme_id'])->name??'';
        $data['after']['sme'] = User::find($data['row']->event_properties['subjectAttributes']['sme_id'])->name??'';
        $data['before']['grade_class'] = GradeClass::find($data['subject']['grade_class_id'])->title??'';
        $data['after']['grade_class'] = GradeClass::find($data['row']->event_properties['subjectAttributes']['grade_class_id'])->title??'';
        $data['row']->by = User::find($data['row']->event_properties['by']);
        $data['module'] = $this->module;
        $data['page_title'] = trans('app.View Subject Log');
        $data['breadcrumb'] = [trans('navigation.Subjects') => route('admin.subjects.get.index')];
        return view($this->parent . '.' . $this->module . '.viewLog', $data);
    }


}
