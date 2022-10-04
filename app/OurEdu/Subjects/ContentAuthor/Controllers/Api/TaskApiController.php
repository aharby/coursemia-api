<?php

namespace App\OurEdu\Subjects\ContentAuthor\Controllers\Api;

use App\OurEdu\Subjects\ContentAuthor\Middleware\Api\ContentAuthorAssignedToTaskMiddleware;
use App\OurEdu\Subjects\UseCases\ReleaseTaskUseCase\ReleaseTaskUseCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Subjects\Events\SubjectModified;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Subjects\SME\Transformers\TaskTransformer;
use App\OurEdu\Subjects\ContentAuthor\Requests\FillResourceRequest;
use App\OurEdu\Subjects\UseCases\PullTaskUseCase\PullTaskUseCaseInterface;
use App\OurEdu\Subjects\ContentAuthor\Middleware\Api\TaskContentAuthorMiddleware;
use App\OurEdu\Subjects\ContentAuthor\Middleware\Api\FillResourceAuthorMiddleware;
use App\OurEdu\Subjects\UseCases\MarkTaskAsDoneUseCase\MarkTaskAsDoneUseCaseInterface;
use App\OurEdu\Subjects\ContentAuthor\Transformers\ResourceSubjectFormatSubjectTransformer;
use App\OurEdu\ResourceSubjectFormats\Repository\ResourceSubjectFormatSubjectRepositoryInterface;
use App\OurEdu\ResourceSubjectFormats\UseCases\FillResourceUseCases\FillResourceUseCaseInterface;

class TaskApiController extends BaseApiController
{
    private $module;
    private $repository;
    private $title;
    private $parserInterface;
    private $pullTaskUseCase;
    private $releaseTaskUseCase;
    private $fillResourceUseCase;
    private $resourceSubjectFormatSubject;
    protected $markAsDoneUseCase;

    public function __construct(
        PullTaskUseCaseInterface $pullTaskUseCase,
        ReleaseTaskUseCase $releaseTaskUseCase,
        FillResourceUseCaseInterface $fillResourceUseCase,
        ParserInterface $parserInterface,
        ResourceSubjectFormatSubjectRepositoryInterface $resourceSubjectFormatSubject,
        MarkTaskAsDoneUseCaseInterface $markAsDoneUseCase
    ) {
        $this->middleware(TaskContentAuthorMiddleware::class)->only(['pullTask']);
        $this->middleware(ContentAuthorAssignedToTaskMiddleware::class)->only(['releaseTask' , 'markTaskAsDone']);
        $this->middleware(FillResourceAuthorMiddleware::class)->only(['fillResource', 'getFillResource']);
        $this->parserInterface = $parserInterface;
        $this->resourceSubjectFormatSubject = $resourceSubjectFormatSubject;
        $this->pullTaskUseCase = $pullTaskUseCase;
        $this->releaseTaskUseCase = $releaseTaskUseCase;
        $this->fillResourceUseCase = $fillResourceUseCase;
//
        $this->markAsDoneUseCase = $markAsDoneUseCase;
    }

    public function pullTask($taskId)
    {
        $user = auth()->user();
        try {
            DB::beginTransaction();
            $data = $this->pullTaskUseCase->pullTask($taskId, $user);
            DB::commit();
            $meta = [
                'message' => trans('task.Task pulled successfully')
            ];

            return response()->json(['meta'=>$meta], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage(),$e->getCode());
        }
    }

    public function releaseTask($taskId)
    {
        $user = auth()->user();
        try {
            DB::beginTransaction();
            $data = $this->releaseTaskUseCase->releaseTask($taskId, $user);
            DB::commit();
            $meta = [
                'message' => trans('task.Task released successfully')
            ];

            return response()->json(['meta'=>$meta], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage(),$e->getCode());
        }
    }

    public function markTaskAsDone($taskId)
    {
        $task = $this->markAsDoneUseCase->markTaskAsDone($taskId);

        $meta = [
            'message' => trans('task.Task marked as done successfully')
        ];

        return response()->json(['meta'=>$meta], 200);
    }


    public function fillResource(FillResourceRequest $request, $resourceId)
    {
        $user = auth()->user();
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        try {
            DB::beginTransaction();
            $this->fillResourceUseCase->fillResource($resourceId, $data, $user);

            DB::commit();


            $include = '';
            $resource = $this->resourceSubjectFormatSubject->findOrFail($resourceId);

            SubjectModified::dispatch([], $resource->subjectFormatSubject->subject->toArray(), 'resource filled');

            return $this->transformDataModInclude($resource, $include, new ResourceSubjectFormatSubjectTransformer(), ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function getFillResource($resourceId)
    {
        $resource = $this->resourceSubjectFormatSubject->findOrFail($resourceId);
        $resource->related_task = Task::where('resource_subject_format_subject_id', $resourceId)->where('is_assigned', 1)->where('is_done', 0)->where('is_expired', 0)->first();
        return $this->transformDataModInclude($resource, 'actions', new ResourceSubjectFormatSubjectTransformer(), ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT);
    }
}
