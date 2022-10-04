<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClass\Repositories;


use App\OurEdu\SchoolAccounts\ClassroomClass\ImportJob;
use Illuminate\Http\Request;

interface ImportJobRepositoryInterface
{
    public function create(array $data) : ImportJob;
}
