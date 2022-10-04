<?php

namespace App\OurEdu\Subjects\UseCases\SubjectStructural\UpdateSubjectStructuralUseCase;

use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Repository\SubjectRepository;
use Illuminate\Database\Eloquent\Collection;

interface UpdateSubjectStructuralUseCaseInterface
{
    public function updateNestedStructural($array, $subjectId, $parentSubjectFormatId = null, bool $isGenerateTask = false, bool $isAptitudeSubject = false, $removedSections = null, $removedResources = null);

    public function getSubjectFormatSubjectTasks(SubjectFormatSubject $subjectFormatSubject, bool $onlyNotAssigned, ?Collection $tasks);

    public function updateParentsProgressOnDelete(SubjectRepository $subjectRepository ,$section);
}
