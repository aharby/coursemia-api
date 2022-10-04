<?php

namespace App\OurEdu\Subjects\Instructor\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Subjects\Instructor\Transformers\SubjectTransformer;

class SubjectApiController extends BaseApiController
{
    private $repository;

    public function __construct(
        SubjectRepositoryInterface $subjectRepository
    ) {
        $this->repository = $subjectRepository;
        $this->user = Auth::guard('api')->user();

    }

    public function viewSubjectSections($subjectId)
    {
        $subject = $this->repository->findOrFail($subjectId);
        $params['view_subject_sections'] = true;
        return $this->transformDataModInclude($subject, 'sections.actions', new SubjectTransformer($params), ResourceTypesEnums::SUBJECT);
    }
}
