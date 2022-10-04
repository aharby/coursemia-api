<?php

namespace App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Transformers\SubjectTransformer;
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
        return $this->transformDataModInclude($section, 'actions , children',new SubjectFormatSubjectTransformer(), ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);
    }
}
