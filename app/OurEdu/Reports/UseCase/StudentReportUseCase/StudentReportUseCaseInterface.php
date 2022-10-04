<?php

namespace App\OurEdu\Reports\UseCase\StudentReportUseCase;

interface StudentReportUseCaseInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function studentReport(array $data): array;
}