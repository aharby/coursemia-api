<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\UseCases\Imports;


use App\OurEdu\SchoolAccounts\ClassroomClass\ImportJob;
use Illuminate\Http\Request;

interface ImportJobsUseCaseInterface
{
    public function create(Request $request) : ImportJob;
}
