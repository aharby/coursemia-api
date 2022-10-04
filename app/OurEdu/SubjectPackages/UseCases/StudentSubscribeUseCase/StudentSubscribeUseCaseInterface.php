<?php

namespace App\OurEdu\SubjectPackages\UseCases\StudentSubscribeUseCase;

interface StudentSubscribeUseCaseInterface
{
    /**
     * @param int $packageId
     * @param int $studentId
     * @return array
     */
    public function subscribePackage(int $packageId,int $studentId): array;
}
