<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClass\Repositories;


use App\OurEdu\SchoolAccounts\ClassroomClass\ImportJob;
use Illuminate\Http\Request;

class ImportJobRepository implements ImportJobRepositoryInterface
{

    /**
     * @param array $data
     * @return ImportJob
     */
    public function create(array $data): ImportJob
    {
        return ImportJob::query()->create($data);
    }
}
