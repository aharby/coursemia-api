<?php

namespace App\OurEdu\Subjects\UseCases\SubjectStructural\GenerateTasksUseCase;

use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Repository\SubjectRepository;

interface GenerateTasksUseCaseInterface
{
    public function generateTaskForResource(SubjectRepository $subjectRepository, ResourceSubjectFormatSubject $resourceObj);
    public function generateBulkTasksForResources(array $resourcesIds);
    public function getAllParentSubjectFormatSubject(SubjectRepository $subjectRepository, $subjectFormatSubjectId, $allParentsArray = []);
    public function generateBulkTasksForResourcesForSubject(int $subjectId) ;
}
