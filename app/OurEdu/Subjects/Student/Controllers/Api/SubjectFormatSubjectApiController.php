<?php

namespace App\OurEdu\Subjects\Student\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Repository\SubjectFormatSubjectRepositoryInterface;
use App\OurEdu\Subjects\Student\Transformers\BreadcrumbsTransformer;
use App\OurEdu\Subjects\Student\Transformers\ResourceSubjectFormatSubjectTransformer;
use App\OurEdu\Subjects\Student\Transformers\SubjectFormatSubjectResourceSubjectFormatSubjectTransformer;
use App\OurEdu\Subjects\UseCases\UpdateProgressUseCase\UpdateProgressUseCaseInterface;

class SubjectFormatSubjectApiController extends BaseApiController
{
    private $repository;
    private $updateProgressUseCase;

    public function __construct(
        SubjectFormatSubjectRepositoryInterface $subjectFormatSubjectRepository,
        UpdateProgressUseCaseInterface $updateProgressUseCase
    )
    {
        $this->repository = $subjectFormatSubjectRepository;
        $this->updateProgressUseCase = $updateProgressUseCase;
    }

    public function viewSubjectFormatSubjectDetails($sectionId)
    {
        $section = $this->repository->findOrFail($sectionId);
        return $this->transformDataModInclude(
            $section,
            'subjectFormatSubjects.resourceSubjectFormatSubject.details,resourceSubjectFormatSubject.details,breadcrumbs',
            new SubjectFormatSubjectResourceSubjectFormatSubjectTransformer(),
            ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
        );
    }

    public function viewBreadCrumbs($sectionId)
    {
        $subjectFormatSubject = SubjectFormatSubject::find($sectionId);
        $parentSctionsIds = getBreadcrumbsIds($subjectFormatSubject,[]);
        return $this->transformDataModInclude($parentSctionsIds, '', new BreadcrumbsTransformer(),
            ResourceTypesEnums::BREADCRUMB);

    }

    public function viewResourceSubjectFormatSubjectDetails($sectionId , $resourceID)
    {
        $student = auth()->user()->student;

        $section = $this->repository->findOrFail($sectionId);
        $resource = $section->resourceSubjectFormatSubject->where('id' , $resourceID)->first();
        $this->updateProgressUseCase->updateProgress($student,$resource->id);
        return $this->transformDataModInclude($resource, 'details,breadcrumbs', new ResourceSubjectFormatSubjectTransformer(['details' => true]), ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT);
    }
}
