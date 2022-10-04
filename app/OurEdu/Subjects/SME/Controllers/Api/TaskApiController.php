<?php

namespace App\OurEdu\Subjects\SME\Controllers\Api;

use App\OurEdu\Options\Option;
use App\OurEdu\Subjects\SME\Transformers\TestTasksTransformer;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\LearningResources\Resource;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Subjects\SME\Transformers\TaskTransformer;
use App\OurEdu\Subjects\SME\Middleware\Api\TaskMiddleware;
use App\OurEdu\Subjects\Repository\TaskRepositoryInterface;
use App\OurEdu\Subjects\SME\Transformers\TaskListTransformer;
use App\OurEdu\Subjects\UseCases\GetTasks\GetTasksUseCaseInterface;
use App\OurEdu\Subjects\SME\Transformers\TaskPerformanceTransformer;
use App\OurEdu\Subjects\SME\Middleware\Api\GetSubjectTasksMiddleware;

class TaskApiController extends BaseApiController
{
    private $module;
    private $repository;
    private $subjectRepository;
    private $title;
    private $getTasksUseCase;
    protected $user;
    protected $filters;

    public function __construct(
        SubjectRepository $subjectRepository,
        TaskRepositoryInterface $taskRepository,
        GetTasksUseCaseInterface $getTasksUseCase
    )
    {
//        $this->middleware(SubjectPolicyMiddleware::class)->except(['getIndex']);
        $this->middleware(TaskMiddleware::class)->except(['getSubjectTasks', 'getAllTasksForTest']);
        $this->middleware(GetSubjectTasksMiddleware::class)->only(['getSubjectTasks']);
        $this->module = 'subjects';
        $this->repository = $taskRepository;
        $this->subjectRepository = $subjectRepository;
        $this->getTasksUseCase = $getTasksUseCase;
        $this->setFilters();

        $this->title = trans('subjects.Subject');
        $this->user = Auth::guard('api')->user();
    }

    public function getAllTasks()
    {
        $user = auth()->user();
        $data = $this->getTasksUseCase->getAllTasks($user, $this->filters);
        $include = '';
        if ($user->type == UserEnums::SME_TYPE) {
            $include .= 'contentAuthor';
        }
        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];

        return $this->transformDataModInclude($data, $include, new TaskTransformer(), ResourceTypesEnums::TASK, $meta);
    }

    public function getSubjectTasks($subjectId)
    {
        $user = auth()->user();
        $data = $this->getTasksUseCase->getSubjectTasks($subjectId, $user, $this->filters);
        $include = '';
        if ($user->type == UserEnums::SME_TYPE) {
            $include .= 'contentAuthor';
        }
        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];

        return $this->transformDataModInclude($data, $include, new TaskTransformer(), ResourceTypesEnums::TASK, $meta);
    }

    public function tasksPerformance()
    {
        $contentAuthorsReport = $this->repository->getSmeTasksPerformance($this->user);

        return $this->transformDataModInclude($contentAuthorsReport, ['user', 'actions'], new TaskPerformanceTransformer(), ResourceTypesEnums::TASK_PERFORMANCE_LIST);
    }

    public function tasksPerformanceList($contentAuthor)
    {
        $tasks = $this->repository->contentAuthorTaskDetails($contentAuthor);

        return $this->transformDataModInclude($tasks['tasks'], '', new TaskListTransformer(), ResourceTypesEnums::TASK);
    }


    public function getView($id)
    {
        $task = $this->repository->findOrFail($id);

        return $this->transformDataModInclude($task, ['subject', 'actions'], new TaskTransformer(), ResourceTypesEnums::TASK);
    }

    protected function setFilters()
    {
        $options = Option::whereIn('type', [
            OptionsTypes::RESOURCE_DIFFICULTY_LEVEL,
            OptionsTypes::RESOURCE_LEARNING_OUTCOME,
        ])->get();

        $this->filters[] = [
            'name' => 'is_assigned',
            'type' => 'select',
            'data' => [
                'no' => trans('app.No'),
                'yes' => trans('app.Yes')
            ],
            'pipes' => 'TrueFalse',
            'trans' => false,
            'value' => request()->get('is_assigned'),
        ];

        $this->filters[] = [
            'name' => 'subject_id',
            'type' => 'select',
            'data' => [],
            'trans' => false,
            'value' => request()->get('subject_id'),
        ];

        $this->filters[] = [
            'name' => 'is_done',
            'type' => 'select',
            'data' => [
                'no' => trans('app.No'),
                'yes' => trans('app.Yes')
            ],
            'pipes' => 'TrueFalse',
            'trans' => false,
            'value' => request()->get('is_done'),
        ];

        $this->filters[] = [
            'name' => 'is_expired',
            'type' => 'select',
            'data' => [
                'no' => trans('app.No'),
                'yes' => trans('app.Yes')
            ],
            'pipes' => 'TrueFalse',
            'trans' => false,
            'value' => request()->get('is_expired'),
        ];

        $this->filters[] = [
            'name' => 'difficulty_level',
            'type' => 'relation',
            'key' => 'accept_criteria->difficulty_level',
            'relation' => 'resourceSubjectFormatSubject',
            'data' => $options->where('type', OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->pluck('title', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('difficulty_level'),
        ];

        $this->filters[] = [
            'name' => 'learning_outcome',
            'type' => 'relation',
            'key' => 'accept_criteria->learning_outcome',
            'relation' => 'resourceSubjectFormatSubject',
            'data' => $options->where('type', OptionsTypes::RESOURCE_LEARNING_OUTCOME)->pluck('title', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('learning_outcome'),
        ];

        $this->filters[] = [
            'name' => 'resource_type',
            'type' => 'relation',
            'key' => 'resource_slug',
            'relation' => 'resourceSubjectFormatSubject',
            'data' => Resource::get()->pluck('title', 'slug')->toArray(),
            'trans' => false,
            'value' => request()->get('resource_type'),
        ];
    }

    // this function testing purposes only
    public function getAllTasksForTest($subjectId)
    {
        $subject = $this->subjectRepository->findOrFail($subjectId);
        $data = $this->repository->getSubjectTasksPaginated($subject, $this->filters);
        $include = 'contentAuthor';

        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];
        return $this->transformDataModInclude($data, $include, new TestTasksTransformer(), ResourceTypesEnums::TASK, $meta);
    }
}
