<?php

namespace App\OurEdu\Subjects\SME\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Subjects\SME\Jobs\UpdateStudentsTotalPointsOnDelete;
use App\OurEdu\Subjects\SME\Requests\SectionStructureRequest;
use App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase\UpdateSubjectStructuralUseCaseInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Subjects\Events\SubjectFormatSubjectModified;
use App\OurEdu\Subjects\Events\SubjectFormatSubjectPausedEvent;
use App\OurEdu\Subjects\Events\SubjectFormatSubjectResumedEvent;
use App\OurEdu\Subjects\Repository\SubjectFormatSubjectRepository;
use App\OurEdu\Subjects\SME\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\Subjects\Repository\SubjectFormatSubjectRepositoryInterface;
use App\OurEdu\Subjects\UseCases\EditResource\EditResourceSubjectFormatSubjectUseCase;
use App\OurEdu\ResourceSubjectFormats\Repository\ResourceSubjectFormatSubjectRepositoryInterface;
use App\OurEdu\Subjects\SME\Transformers\EditResources\SubjectFormatSubjectResourceSubjectFormatSubjectTransformer;
use App\OurEdu\Subjects\SME\Transformers\SingleSectionTransformer;

class SubjectFormatSubjectApiController extends BaseApiController
{
    private $repository;
    private $resourceSubjectFormatRepository;
    private $parserInterface;
    private $editResourceSubjectFormatSubjectUseCase;
    private $updateSubjectStructuralUseCase;
    private $subjectRepository;

    public function __construct(
        ParserInterface $parserInterface,
        SubjectFormatSubjectRepositoryInterface $subjectFormatSubjectRepository,
        ResourceSubjectFormatSubjectRepositoryInterface $resourceSubjectFormatRepository,
        EditResourceSubjectFormatSubjectUseCase $editResourceSubjectFormatSubjectUseCase,
        UpdateSubjectStructuralUseCaseInterface $updateSubjectStructuralUseCase,
        SubjectRepositoryInterface $subjectRepository
    )
    {
        $this->repository = $subjectFormatSubjectRepository;
        $this->resourceSubjectFormatRepository = $resourceSubjectFormatRepository;
        $this->parserInterface = $parserInterface;
        $this->editResourceSubjectFormatSubjectUseCase = $editResourceSubjectFormatSubjectUseCase;
        $this->updateSubjectStructuralUseCase = $updateSubjectStructuralUseCase;
        $this->subjectRepository = $subjectRepository;

    }

    public function viewSubjectFormatSubjectDetails($sectionId)
    {
        $section = $this->repository->findOrFail($sectionId);

        return $this->transformDataModInclude($section, 'subjectFormatSubjects,resourceSubjectFormatSubject', new SubjectFormatSubjectResourceSubjectFormatSubjectTransformer(), ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);
    }

    public function pauseUnPauseSubjectFormat($subjectFormatId)
    {
        $subjectFormat = $this->repository->findOrFail($subjectFormatId);

        if ($subjectFormat->subject->is_aptitude) {
            return formatErrorValidation([
                'status' => 403,
                'title' => 'cant pause subject',
                'detail' => trans('subject.Cant Pause Subject')
            ], 403);
        }

        $subjectFormatRepo = new SubjectFormatSubjectRepository($subjectFormat);

        $subjectFormatRepo->toggleActive();

        if ($subjectFormat->fresh()->is_active) {
            SubjectFormatSubjectModified::dispatch([], $subjectFormat->toArray(), 'Section resumed');
            SubjectFormatSubjectResumedEvent::dispatch($subjectFormat);
            $meta = [
                'message' => trans('subject.Section un-paused successfully')
            ];
        } else {
            SubjectFormatSubjectModified::dispatch([], $subjectFormat->toArray(), 'Section paused');
            SubjectFormatSubjectPausedEvent::dispatch($subjectFormat);
            $meta = [
                'message' => trans('subject.Section paused successfully')
            ];
        }

        return response()->json(['meta' => $meta], 200);
    }

    public function getSectionStructure($sectionId)
    {
        $subjectFormatSubjectsData = $this->repository->findOrFail($sectionId);
        return $this->transformDataModInclude($subjectFormatSubjectsData, '', new SingleSectionTransformer(['method' => 'GET']), ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);
    }

    public function createOrUpdateSectionStructure(SectionStructureRequest $request, $subjectId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        try {
            DB::beginTransaction();
//            $subjectRepo = new SubjectRepository($subjectFormatSubject->subject);
            $subjectRepo = new SubjectRepository($this->subjectRepository->findOrFail($subjectId));
            $parentSubjectFormatId = $data->parent_subject_format_id ?? null;
            $sectionId = $this->updateSubjectStructuralUseCase->createOrUpdateSection($subjectRepo, $subjectId, $data, $parentSubjectFormatId);
            $section = $this->repository->findOrFail($sectionId);
            DB::commit();

            return $this->transformDataModInclude($section, '', new SubjectFormatSubjectTransformer(), ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        }
    }

    public function deleteSection($sectionId)
    {
        $subjectFormatSubjectsData = $this->repository->findOrFail($sectionId);

        $subject =  $subjectFormatSubjectsData->subject;

        $repo = new SubjectRepository($subject);

        UpdateStudentsTotalPointsOnDelete::dispatch( $subjectFormatSubjectsData->total_points , $subjectFormatSubjectsData);

        $this->updateSubjectStructuralUseCase->updateParentsProgressOnDelete($repo,$subjectFormatSubjectsData);

        $subject->decrement('total_points', $subjectFormatSubjectsData->total_points );

        $subjectFormatSubjectsData->delete();
        $meta = [
            'message' => trans('subject.Section deleted successfully')
        ];

        return response()->json(['meta' => $meta], 200);
    }



}
