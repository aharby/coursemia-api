<?php


namespace App\OurEdu\GeneralQuizzes\Subject\Controllers;


use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Subject\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\GeneralQuizzes\Subject\Transformers\SubjectTransformer;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Repository\SubjectFormatSubjectRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;

class SubjectSectionsController extends BaseApiController
{
    /**
     * @var SubjectRepositoryInterface
     */
    private $subjectRepository;
    /**
     * @var SubjectFormatSubjectRepositoryInterface
     */
    private $subjectFormatSubjectRepository;

    /**
     * SubjectSections constructor.
     * @param SubjectRepositoryInterface $subjectRepository
     * @param SubjectFormatSubjectRepositoryInterface $subjectFormatSubjectRepository
     */
    public function __construct(SubjectRepositoryInterface $subjectRepository, SubjectFormatSubjectRepositoryInterface $subjectFormatSubjectRepository)
    {
        $this->subjectRepository = $subjectRepository;
        $this->subjectFormatSubjectRepository = $subjectFormatSubjectRepository;
    }

    public function SubjectSections(Subject $subject)
    {
        return $this->transformDataModInclude($subject, 'sections', new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
    }

    public function subsections(SubjectFormatSubject $section)
    {
        $subjectFormatSubjects = $section->childSubjectFormatSubject()->orderBy('list_order_key', 'ASC')->get();

        return $this->transformDataMod($subjectFormatSubjects, new SubjectFormatSubjectTransformer(), ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);
    }

    public function OurEduSubjectSections($subjectUuid)
    {
        $subject = $this->subjectRepository->firstOrFailWithUuid($subjectUuid);
        return $this->transformDataModInclude($subject, 'sections', new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
    }
}
