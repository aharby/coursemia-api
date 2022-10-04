<?php

namespace App\OurEdu\Subjects\SME\Controllers\Api;

use App\Exceptions\ErrorResponseException;
use App\OurEdu\Subjects\Repository\SubjectFormatSubjectRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Subjects\Repository\TaskRepository;
use App\OurEdu\Subjects\Repository\TaskRepositoryInterface;
use App\OurEdu\Subjects\SME\Jobs\UpdateStudentsTotalPointsOnDelete;
use App\OurEdu\Subjects\SME\Transformers\ResourceSubjectFormatSubjectTransformer;
use App\OurEdu\Subjects\SME\Transformers\SingleSectionTransformer;
use App\OurEdu\Subjects\SME\Transformers\TaskListTransformer;
use App\OurEdu\Subjects\UseCases\SubjectStructural\GenerateTasksUseCase\GenerateTasksUseCaseInterface;
use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase\UpdateSubjectStructuralUseCaseInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Subjects\SME\Requests\EditResourceRequest;
use App\OurEdu\ResourceSubjectFormats\Events\ResourceFormatSubjectFormatPausedEvent;
use App\OurEdu\ResourceSubjectFormats\Events\ResourceFormatSubjectFormatResumedEvent;
use App\OurEdu\Subjects\UseCases\EditResource\EditResourceSubjectFormatSubjectUseCase;
use App\OurEdu\ResourceSubjectFormats\Repository\ResourceSubjectFormatSubjectRepository;
use App\OurEdu\ResourceSubjectFormats\Repository\ResourceSubjectFormatSubjectRepositoryInterface;
use App\OurEdu\Subjects\SME\Transformers\EditResources\ResourceSubjectFormatSubjectTransformerDetails;
use Throwable;

class ResourceSubjectFormatApiController extends BaseApiController
{
    private $resourceSubjectFormatRepository;
    private $parserInterface;
    private $editResourceSubjectFormatSubjectUseCase;
    private $updateSubjectStructuralUseCase;
    private $subjectFormatSubjectRepository;
    private $generateTasksUseCase;
    protected $taskRepository;

    public function __construct(
        ParserInterface $parserInterface,
        ResourceSubjectFormatSubjectRepositoryInterface $resourceSubjectFormatRepository,
        EditResourceSubjectFormatSubjectUseCase $editResourceSubjectFormatSubjectUseCase,
        UpdateSubjectStructuralUseCaseInterface $updateSubjectStructuralUseCase,
        SubjectFormatSubjectRepositoryInterface $subjectFormatSubjectRepository,
        GenerateTasksUseCaseInterface $generateTasksUseCase,
        TaskRepositoryInterface $taskRepository
    )
    {
        $this->resourceSubjectFormatRepository = $resourceSubjectFormatRepository;
        $this->parserInterface = $parserInterface;
        $this->editResourceSubjectFormatSubjectUseCase = $editResourceSubjectFormatSubjectUseCase;
        $this->taskRepository = $taskRepository;
        $this->updateSubjectStructuralUseCase = $updateSubjectStructuralUseCase;
        $this->subjectFormatSubjectRepository = $subjectFormatSubjectRepository;
        $this->generateTasksUseCase = $generateTasksUseCase;

    }

    public function getEditResourceSubjectFormat($resourceSubjectFormatId)
    {
        try {
            $resource = $this->resourceSubjectFormatRepository->findOrFail($resourceSubjectFormatId);
            $include = 'resourceSubjectFormatSubjectData';
            return $this->transformDataModInclude($resource, $include, new ResourceSubjectFormatSubjectTransformerDetails(), ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT);
        } catch (\Throwable $e) {
            Log::error($e);
//            throw $e;
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function postCreateResourceSubjectFormatStructure(Request $request, $subjectFormatId)
    {

        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        try {
            DB::beginTransaction();
            $subjectFormatSubject = $this->subjectFormatSubjectRepository->findOrFail($subjectFormatId);
            $subjectRepo = new SubjectRepository($subjectFormatSubject->subject);
            $resource = $this->updateSubjectStructuralUseCase->createOrUpdateResource($subjectRepo, $data, $subjectFormatId, false);
            $subjectRepo->updateTotalPoints();
            DB::commit();

            $include = '';
            return $this->transformDataModInclude(
                $resource,
                $include,
                new ResourceSubjectFormatSubjectTransformerDetails(),
                ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        }
    }

    public function generateTasks(Request $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $tasks = $this->generateTasksUseCase->generateBulkTasksForResources($data->resources);
        return $this->transformDataModInclude($tasks, '', new TaskListTransformer(), ResourceTypesEnums::TASK);

    }

    public function generateSubjectTasks(Request $request, $subjectId)
    {

        $tasks = $this->generateTasksUseCase->generateBulkTasksForResourcesForSubject($subjectId);
        return $this->transformDataModInclude($tasks, '', new TaskListTransformer(), ResourceTypesEnums::TASK);

    }

    public function postEditResourceSubjectFormat(EditResourceRequest $request, $resourceSubjectFormatId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        try {
            DB::beginTransaction();
            $useCase = $this->editResourceSubjectFormatSubjectUseCase->editResourceContent($resourceSubjectFormatId, $data);
            DB::commit();

            if (!empty($useCase) and isset($useCase['status']) and $useCase['status'] != 200) {
                return formatErrorValidation($useCase);
            }

            $resource = $this->resourceSubjectFormatRepository->findOrFail($resourceSubjectFormatId);
            $include = '';
            return $this->transformDataModInclude(
                $resource,
                $include,
                new ResourceSubjectFormatSubjectTransformerDetails(),
                ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        }
    }

    public function pauseUnPauseResource($resourceId)
    {
        $resource = $this->resourceSubjectFormatRepository->findOrFail($resourceId);

        if ($resource->subjectFormatSubject->subject->is_aptitude) {
            return formatErrorValidation([
                'status' => 403,
                'title' => 'cant pause subject',
                'detail' => trans('subject.Cant Pause Subject')
            ], 403);
        }

        $resourceRepo = new ResourceSubjectFormatSubjectRepository($resource);

        $resourceRepo->toggleActive();

        if ($resource->fresh()->is_active) {
            ResourceFormatSubjectFormatResumedEvent::dispatch($resource);
            $meta = [
                'message' => trans('subject.Resource un-paused successfully')
            ];
        } else {
            ResourceFormatSubjectFormatPausedEvent::dispatch($resource);
            $meta = [
                'message' => trans('subject.Resource paused successfully')
            ];
        }
        return response()->json(['meta' => $meta], 200);
    }

    public function markTaskAsDone($taskId)
    {
        $user = Auth::guard('api')->user();
        $task = $this->taskRepository->findOrFail($taskId);

        // already done
        if ($task->is_done) {
            throw new ErrorResponseException(trans('api.Task already done'));
        }

        if ($task->is_paused) {
            throw new ErrorResponseException(trans('api.Task is paused at the moment'));
        }

        $taskRepository = new TaskRepository($task);

        $taskRepository->update([
            'is_done' => 1,
            'is_active' => 0
        ]);

        return $task;
    }

    public function getSingleResource($id)
    {
        try {
            $resourceSubjectFormatSubject = $this->resourceSubjectFormatRepository->findOrFail($id);
            return $this->transformDataModInclude($resourceSubjectFormatSubject, '', new ResourceSubjectFormatSubjectTransformerDetails(), ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT);
        } catch (Throwable $exception) {
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
//            throw $e;
        }

    }

    public function deleteSingleResource($id)
    {
        $resourceSubjectFormatSubject = $this->resourceSubjectFormatRepository->findOrFail($id);

        $repo = new SubjectRepository($resourceSubjectFormatSubject->subjectFormatSubject->subject);

        UpdateStudentsTotalPointsOnDelete::dispatch($resourceSubjectFormatSubject->total_points ,$resourceSubjectFormatSubject->subjectFormatSubject, $resourceSubjectFormatSubject);

        $this->updateSubjectStructuralUseCase->updateProgress($repo,$id,$resourceSubjectFormatSubject->resource_slug,$resourceSubjectFormatSubject->subjectFormatSubject->id,false);

        $resourceSubjectFormatSubject->subjectFormatSubject->subject->decrement('total_points',$resourceSubjectFormatSubject->total_points);

        if($resourceSubjectFormatSubject->task) {
            $resourceSubjectFormatSubject->task->delete();
        }

       $resourceSubjectFormatSubject->delete();


        $meta = [
            'message' => trans('subject.Section deleted successfully')
        ];

        return response()->json(['meta' => $meta], 200);
    }
}
