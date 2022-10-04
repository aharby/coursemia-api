<?php

namespace App\OurEdu\Reports\UseCase\SMEListReportsUseCase;

interface SMEListReportsUseCaseInterface
{
    public function listReports($subjectId,
                                $subjectFormatSubjectId,
                                $resourceId,
                                $reportedSections,
                                $reportedResources);
}
