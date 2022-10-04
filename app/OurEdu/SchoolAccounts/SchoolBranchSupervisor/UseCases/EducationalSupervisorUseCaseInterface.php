<?php
namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\UseCases;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Requests\EducationalSupervisorRequest;

interface EducationalSupervisorUseCaseInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function updateEducationalSupervisor(EducationalSupervisorRequest $request,$educationalSupervisor);
}
