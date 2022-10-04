<?php

namespace App\OurEdu\Subjects\Admin\Controllers;

use App\OurEdu\LearningResources\Resource;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Repository\TaskRepositoryInterface;

class TasksController extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $filters = [];

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->module = 'tasks';
        $this->repository = $taskRepository;
        $this->title = trans('subjects.Tasks');
        $this->parent = ParentEnum::ADMIN;
    }

    public function getIndex()
    {
        $this->setFilters();
        $data['filters'] = $this->filters;
        $data['tasks'] = $this->repository->all($this->filters);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = [trans('navigation.Subjects') => route('admin.subjects.get.index')];
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function setFilters()
    {
        $options = Option::whereIn('type', [
            OptionsTypes::RESOURCE_DIFFICULTY_LEVEL,
            OptionsTypes::RESOURCE_LEARNING_OUTCOME,
        ])->get();

        $this->filters[] = [
            'name' => 'subject_id',
            'type' => 'select',
            'data' => Subject::pluck('name', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('subject_id'),
            'attributes' => [
                'class' => 'form-control',
                'label' => trans('subject.subject_name'),
                'placeholder' => trans('subject.subject_name'),
            ]
        ];
        $this->filters[] = [
            'name' => 'difficulty_level',
            'type' => 'relation',
            'key' => 'accept_criteria->difficulty_level',
            'relation' => 'resourceSubjectFormatSubject',
            'data' => $options->where('type', OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->pluck('title', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('difficulty_level'),
            'attributes' => [
                'class' => 'form-control',
                'label' => trans('subject.difficulty_level'),
                'placeholder' => trans('subject.difficulty_level'),
            ]
        ];

        $this->filters[] = [
            'name' => 'learning_outcome',
            'type' => 'relation',
            'key' => 'accept_criteria->learning_outcome',
            'relation' => 'resourceSubjectFormatSubject',
            'data' => $options->where('type', OptionsTypes::RESOURCE_LEARNING_OUTCOME)->pluck('title', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('learning_outcome'),
            'attributes' => [
                'class' => 'form-control',
                'label' => trans('subject.learning_outcome'),
                'placeholder' => trans('subject.learning_outcome'),
            ]
        ];
        $this->filters[] = [
            'name' => 'resource_type',
            'type' => 'relation',
            'key' => 'resource_slug',
            'relation' => 'resourceSubjectFormatSubject',
            'data' => Resource::get()->pluck('title', 'slug')->toArray(),
            'trans' => false,
            'value' => request()->get('resource_type'),
            'attributes' => [
                'class' => 'form-control',
                'label' => trans('subject.resource_type'),
                'placeholder' => trans('subject.resource_type'),
            ]
        ];
    }

    public function contentAuthorTask()
    {
        $data['authors'] = $this->repository->contentAuthorTask();
        $data['page_title'] = trans('tasks.Content Author Tasks');
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.contentAuthorTask', $data);
    }

    public function contentAuthorTaskDetails($contentAuthor)
    {
        $contentAuthorTaskDetails = $this->repository->contentAuthorTaskDetails($contentAuthor);
        $data['tasks'] = $contentAuthorTaskDetails['tasks'];
        $data['contentAuthor'] = $contentAuthorTaskDetails['contentAuthor'];
        $data['options'] = Option::with('translations')->get();
        $data['page_title'] = trans('tasks.Content Author Tasks Details');
        $data['breadcrumb'] = [$this->title => route('admin.tasks.get.content.author.tasks')];
        return view($this->parent . '.' . $this->module . '.contentAuthorTaskDetails', $data);
    }
}
