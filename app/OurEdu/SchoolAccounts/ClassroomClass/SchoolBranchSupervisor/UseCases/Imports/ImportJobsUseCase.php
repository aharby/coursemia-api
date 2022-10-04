<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\UseCases\Imports;


use App\OurEdu\SchoolAccounts\ClassroomClass\ImportJob;
use App\OurEdu\SchoolAccounts\ClassroomClass\Repositories\ImportJobRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Enums\ImportJobsStatusEnums;
use Illuminate\Http\Request;


class ImportJobsUseCase implements ImportJobsUseCaseInterface
{
    /**
     * @var UploaderUseCase
     */
    private $uploaderUseCase;
    /**
     * @var ImportJobRepositoryInterface
     */
    private $repository;

    /**
     * ImportJobsUseCase constructor.
     * @param UploaderUseCase $uploaderUseCase
     * @param ImportJobRepositoryInterface $repository
     */
    public function __construct(UploaderUseCase $uploaderUseCase, ImportJobRepositoryInterface $repository)
    {
        $this->uploaderUseCase = $uploaderUseCase;
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return ImportJob
     */
    public function create(Request $request): ImportJob
    {
        $fileName = "";
        if ($request->hasFile("excel-data")) {
            $fileName = $this->uploaderUseCase->upload($request->file("excel-data"), "class-scheduled-sessions-data");
        }

        $importJobData = [
            "classroom_id" => $request->get("classroom_id"),
            "filename" => $fileName,
            "status" => ImportJobsStatusEnums::PENDING,
        ];

        return $this->repository->create($importJobData);
    }
}
