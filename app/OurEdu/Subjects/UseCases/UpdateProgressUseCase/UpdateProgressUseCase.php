<?php

namespace App\OurEdu\Subjects\UseCases\UpdateProgressUseCase;

use App\OurEdu\LearningResources\Enums\LearningResourcesPointsEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Progress\SubjectFormatProgressStudent;
use App\OurEdu\Subjects\Repository\StudentProgress\ResourceProgressStudentRepository;
use App\OurEdu\Subjects\Repository\StudentProgress\SubjectFormatProgressStudentRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Subjects\UseCases\SubjectStructural\GenerateTasksUseCase\GenerateTasksUseCase;


class UpdateProgressUseCase implements UpdateProgressUseCaseInterface
{

    private $subjectProgressRepo;
    private $resourceProgressRepo;
    private $subjectRepository;

    public function __construct(SubjectFormatProgressStudentRepositoryInterface $subjectFormatProgressStudentRepository, ResourceProgressStudentRepository $resourceProgressStudentRepository, SubjectRepositoryInterface $subjectRepository)
    {
        $this->subjectProgressRepo = $subjectFormatProgressStudentRepository;
        $this->resourceProgressRepo = $resourceProgressStudentRepository;
        $this->subjectRepository = $subjectRepository;
    }


    public function updateProgress($student, $resourceSubjectFormatId)
    {
        $resource = $this->subjectRepository->findOrFailResourceSubject($resourceSubjectFormatId);
        if (!isset(LearningResourcesPointsEnums::getLearningResourcesPointsEnums()[$resource->resource_slug])) {
            return;
        }

        $subject = $resource->subjectFormatSubject->subject;

        $subjectRepo = new SubjectRepository($subject);

        $generateTasksUseCase = new GenerateTasksUseCase($subjectRepo);

        $parents = $generateTasksUseCase->getAllParentSubjectFormatSubject($subjectRepo, $resource->subject_format_subject_id);

        $parents[] = $resource->subject_format_subject_id;

        $points = LearningResourcesPointsEnums::getLearningResourcesPointsEnums()[$resource->resource_slug];

        $resourceData = [
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'subject_format_id' => $resource->subject_format_subject_id,
            'resource_slug' => $resource->resource_slug,
            'resource_id' => $resource->id,
        ];
        $returnedArr = $this->resourceProgressRepo->incrementPoints($resourceData, $points);

        if (isset($returnedArr['viewed'])) {
            foreach ($parents as $sectionFormatId) {
                $data = [
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'subject_format_id' => $sectionFormatId,
                ];
                $this->subjectProgressRepo->incrementPoints($data, $points);
            }

            return $resource;
        }
    }
}
