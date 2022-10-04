<?php

namespace App\OurEdu\QuestionReport\ContentAuthor\Controllers\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\LearningResources\Resource;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\QuestionReport\ContentAuthor\Middleware\Api\FillResourceAuthorMiddleware;
use App\OurEdu\QuestionReport\ContentAuthor\Middleware\Api\TaskContentAuthorMiddleware;
use App\OurEdu\QuestionReport\ContentAuthor\Requests\FillResourceRequest;
use App\OurEdu\QuestionReport\ContentAuthor\Transformers\QuestionReportTaskTransformer;
use App\OurEdu\QuestionReport\ContentAuthor\Transformers\Questions\QuestionDragDropTransformer;
use App\OurEdu\QuestionReport\ContentAuthor\Transformers\Questions\QuestionMatchingTransformer;
use App\OurEdu\QuestionReport\ContentAuthor\Transformers\Questions\QuestionMultiMatchingTransformer;
use App\OurEdu\QuestionReport\ContentAuthor\Transformers\Questions\QuestionMultipleChoiceTransformer;
use App\OurEdu\QuestionReport\ContentAuthor\Transformers\Questions\QuestionTrueFalseTransformer;
use App\OurEdu\QuestionReport\ContentAuthor\Transformers\Questions\QuestionHotspotTransformer;
use App\OurEdu\QuestionReport\Repository\QuestionReportRepository;
use App\OurEdu\QuestionReport\Repository\QuestionReportTaskRepository;
use App\OurEdu\QuestionReport\UseCases\FillResource\FillResourceUseCaseInterface;
use App\OurEdu\QuestionReport\UseCases\GetQuestionReportTasks\GetQuestionReportTasksUseCase;
use App\OurEdu\QuestionReport\UseCases\GetQuestionReportTasks\GetQuestionReportTasksUseCaseInterface;
use App\OurEdu\QuestionReport\UseCases\MarkQuestionReportTaskAsDoneUseCase\MarkQuestionReportTaskAsDoneUseCaseInterface;
use App\OurEdu\QuestionReport\UseCases\PullQuestionReportTasks\PullQuestionReportTasksUseCaseInterface;
use App\OurEdu\QuestionReport\UseCases\ReportQuestionReportUseCase\ReportQuestionReportUseCase;
use App\OurEdu\Subjects\UseCases\MarkTaskAsDoneUseCase\MarkTaskAsDoneUseCaseInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class QuestionReportTaskController extends BaseApiController
{

    private $module;
    private $repository;
    private $reportQuestionReportUseCase;
    private $title;
    private $getQuestionReportTasksUseCase;
    private $pullQuestionReportTasksUseCase;
    private $fillResourceUseCase;
    private $questionReportTaskRepository;
    private $markAsDoneUseCase;
    protected $filters;
    public function __construct(
        QuestionReportRepository $questionReportRepository,
        QuestionReportTaskRepository $questionReportTaskRepository,
        ReportQuestionReportUseCase $reportQuestionReportUseCase,
        GetQuestionReportTasksUseCaseInterface $getQuestionReportTasksUseCase,
        PullQuestionReportTasksUseCaseInterface $pullQuestionReportTasksUseCase,
        FillResourceUseCaseInterface $fillResourceUseCase,
        MarkQuestionReportTaskAsDoneUseCaseInterface $markAsDoneUseCase,
        ParserInterface $parserInterface
    ) {
        $this->repository = $questionReportRepository;
        $this->reportQuestionReportUseCase = $reportQuestionReportUseCase;
        $this->getQuestionReportTasksUseCase = $getQuestionReportTasksUseCase;
        $this->pullQuestionReportTasksUseCase = $pullQuestionReportTasksUseCase;
        $this->fillResourceUseCase = $fillResourceUseCase;
        $this->questionReportTaskRepository = $questionReportTaskRepository;
        $this->markAsDoneUseCase = $markAsDoneUseCase;
        $this->parserInterface = $parserInterface;

        $this->middleware(TaskContentAuthorMiddleware::class)->only(['pullTask']);
        $this->middleware(FillResourceAuthorMiddleware::class)->only(['fillResource', 'getFillResource']);

    }

    public function getAllTasks()
    {
        $user = auth()->user();
        $this->setFilters();
        $data = $this->getQuestionReportTasksUseCase->getAllTasks($user, $this->filters);

        $include = '';
        return $this->transformDataModInclude($data, $include, new QuestionReportTaskTransformer(), ResourceTypesEnums::TASK);

    }

    public function getSubjectTasks($subjectId)
    {
        $this->setFilters();

        $user = auth()->user();
        $data = $this->getQuestionReportTasksUseCase->getSubjectTasks($subjectId, $user , $this->setFilters());
        $include = '';
        return $this->transformDataModInclude($data, $include, new QuestionReportTaskTransformer(), ResourceTypesEnums::TASK);
    }

    public function pullTask($taskId)
    {
        $user = auth()->user();
        try {
            DB::beginTransaction();
            $data = $this->pullQuestionReportTasksUseCase->pullTask($taskId, $user);
            DB::commit();
            $meta = [
                'message' => trans('task.Task pulled successfully')
            ];
            return response()->json(['meta'=>$meta], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function fillResource($taskId , FillResourceRequest $request)
    {
        $task = $this->questionReportTaskRepository->findOrFail($taskId);//
        $questionId = $task->question_id;
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $user = auth()->user();

        try {

            DB::beginTransaction();
            $question = $this->fillResourceUseCase->fillResource($questionId, $data);
            DB::commit();

            $include = '';
            $question->slug = $data->slug;
            switch ($data->slug) {
                case LearningResourcesEnums::TRUE_FALSE:
                    return $this->transformDataModInclude($question, $include ,new QuestionTrueFalseTransformer(),
                        ResourceTypesEnums::QUESTION_EXAM_DATA);
                    break;
                case LearningResourcesEnums::MULTI_CHOICE:
                    return $this->transformDataModInclude($question, $include , new QuestionMultipleChoiceTransformer(),
                        ResourceTypesEnums::QUESTION_EXAM_DATA);
                    break;
                case LearningResourcesEnums::DRAG_DROP:
                    return $this->transformDataModInclude($question,$include , new QuestionDragDropTransformer(),
                        ResourceTypesEnums::QUESTION_EXAM_DATA);
                    break;
                case LearningResourcesEnums::MATCHING:
                    return $this->transformDataModInclude($question, $include , new QuestionMatchingTransformer(),
                        ResourceTypesEnums::QUESTION_EXAM_DATA);
                    break;
                case LearningResourcesEnums::MULTIPLE_MATCHING:
                    return $this->transformDataModInclude($question, $include , new QuestionMultiMatchingTransformer(),
                        ResourceTypesEnums::QUESTION_EXAM_DATA);
                    break;
                case LearningResourcesEnums::HOTSPOT:
                    return $this->transformDataModInclude($question, $include , new QuestionHotspotTransformer(),
                        ResourceTypesEnums::QUESTION_EXAM_DATA);
                    break;
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function getFillResource($taskId) {
        $task = $this->questionReportTaskRepository->findOrFail($taskId);
        $question = $task->questionable()->get()->first();
        $question->slug = $task->slug;
        $question->task_id = $taskId;
        $question->related_task = $task;
        $slug = $task->slug;
        $include = '';

        switch ($slug) {
            case LearningResourcesEnums::TRUE_FALSE:
                return $this->transformDataModInclude($question, $include ,new QuestionTrueFalseTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA);
                break;
            case LearningResourcesEnums::MULTI_CHOICE:
                return $this->transformDataModInclude($question, $include , new QuestionMultipleChoiceTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA);
                break;
            case LearningResourcesEnums::DRAG_DROP:
                return $this->transformDataModInclude($question,$include , new QuestionDragDropTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA);
                break;
            case LearningResourcesEnums::MATCHING:
                return $this->transformDataModInclude($question, $include , new QuestionMatchingTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA);
                break;
            case LearningResourcesEnums::MULTIPLE_MATCHING:
                return $this->transformDataModInclude($question, $include , new QuestionMultiMatchingTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA);
                break;
            case LearningResourcesEnums::HOTSPOT:
                return $this->transformDataModInclude($question, $include , new QuestionHotspotTransformer(),
                    ResourceTypesEnums::QUESTION_EXAM_DATA);
                break;

        }
    }

    public function markTaskAsDone($taskId)
    {
        $task = $this->markAsDoneUseCase->markTaskAsDone($taskId);

        $meta = [
            'message' => trans('task.Task marked as done successfully')
        ];

        $include = '';
        return $this->transformDataModInclude($task, $include, new QuestionReportTaskTransformer(), ResourceTypesEnums::TASK, $meta);
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
                'false' => trans('app.No'),
                'true' => trans('app.Yes')
            ],
            'trans' => false,
            'value' => request()->get('is_assigned'),
        ];

        $this->filters[] = [
            'name' => 'subject_id',
            'type' => 'id',
            'data' => [],
            'trans' => false,
            'value' => request()->get('subject_id'),
        ];

        $this->filters[] = [
            'name' => 'is_done',
            'type' => 'select',
            'data' => [
                'false' => trans('app.No'),
                'true' => trans('app.Yes')
            ],
            'trans' => false,
            'value' => request()->get('is_done'),
        ];

        $this->filters[] = [
            'name' => 'is_expired',
            'type' => 'select',
            'data' => [
                'false' => trans('app.No'),
                'true' => trans('app.Yes')
            ],
            'trans' => false,
            'value' => request()->get('is_expired'),
        ];

        $this->filters[] = [
            'name' => 'difficulty_level',
            'type' => 'relation',
            'key' => 'accept_criteria->difficulty_level' ,
            'relation' => 'resourceSubjectFormatSubject' ,
            'data' => $options->where('type', OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->pluck('title', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('difficulty_level'),
        ];

        $this->filters[] = [
            'name' => 'learning_outcome',
            'type' => 'relation',
            'key' => 'accept_criteria->learning_outcome' ,
            'relation' => 'resourceSubjectFormatSubject' ,
            'data' => $options->where('type', OptionsTypes::RESOURCE_LEARNING_OUTCOME)->pluck('title', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('learning_outcome'),
        ];

        $this->filters[] = [
            'name' => 'resource_type',
            'type' => 'relation',
            'key' => 'resource_slug' ,
            'relation' => 'resourceSubjectFormatSubject' ,
            'data' => Resource::get()->pluck('title', 'slug')->toArray(),
            'trans' => false,
            'value' => request()->get('resource_type'),
        ];
    }

}
