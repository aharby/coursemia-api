<?php

namespace App\OurEdu\Subjects\UseCases\UpdateProgressUseCase;

interface UpdateProgressUseCaseInterface
{
    public function updateProgress($student, $resourceSubjectFormatId);

}
