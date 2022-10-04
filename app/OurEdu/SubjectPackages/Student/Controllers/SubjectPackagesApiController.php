<?php

namespace App\OurEdu\SubjectPackages\Student\Controllers;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SubjectPackages\Repository\SubjectPackageRepositoryInterface;
use App\OurEdu\SubjectPackages\Student\Middleware\AvailablePackagesMiddleware;
use App\OurEdu\SubjectPackages\Student\Transformers\ListPackagesTransformer;
use App\OurEdu\SubjectPackages\Student\Transformers\PackageTransformer;
use App\OurEdu\SubjectPackages\UseCases\StudentSubscribeUseCase\StudentSubscribeUseCaseInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use Illuminate\Support\Facades\Log;

class SubjectPackagesApiController extends BaseApiController
{
    private $subjectPackageRepository;
    private $subscribePackageUseCase;
    private $studentRepository;

    public function __construct(
        SubjectPackageRepositoryInterface $subjectPackageRepository,
        StudentSubscribeUseCaseInterface $subscribePackageUseCase,
        StudentRepositoryInterface $studentRepository
    )
    {
        $this->subjectPackageRepository = $subjectPackageRepository;
        $this->subscribePackageUseCase = $subscribePackageUseCase;
        $this->studentRepository = $studentRepository;
        $this->middleware(AvailablePackagesMiddleware::class)->only(['postSubscribePackage']);
    }

    public function getAvailablePackages()
    {
        try {
            $student = auth()->user()->student;
            $studentData = [
                'class_id' => $student->class_id,
                'educational_system_id' => $student->educational_system_id,
                'academical_years_id' => $student->academical_year_id,
                'country_id' => auth()->user()->country_id
            ];
            $data = $this->subjectPackageRepository->paginateWhereStudent($studentData);
            $include = '';
            return $this->transformDataModInclude($data, $include, new ListPackagesTransformer(), ResourceTypesEnums::SUBJECT_PACKAGE);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function viewPackage($packageId, $studentId = null)
    {
        try {
            if (isset($studentId)) {
                $student = $this->studentRepository->findOrFail($studentId);
            }
            $package = $this->subjectPackageRepository->findOrFail($packageId);
            $include = '';
            return $this->transformDataModInclude($package, $include, new PackageTransformer([], $student->user ?? null)
                , ResourceTypesEnums::SUBJECT_PACKAGE);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function postSubscribePackage($packageId)
    {
        try {
            $studentId = auth()->user()->student->id;
            $subscribePackage = $this->subscribePackageUseCase->subscribePackage($packageId, $studentId);
            if ($subscribePackage['status'] == 200) {
                return response()->json([
                    'meta' => [
                        'message' => trans('app.Subscribed Successfully')
                    ]
                ]);
            } else {
                return formatErrorValidation($subscribePackage);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
        }
    }


    public function listSubjectPackagesForStudent($studentId)
    {
        $student = $this->studentRepository->findOrFail($studentId);
        $studentData = [
            'class_id' => $student->class_id,
            'educational_system_id' => $student->educational_system_id,
            'academical_years_id' => $student->academical_year_id,
            'country_id' => auth()->user()->country_id
        ];

        $subjectPackages = $this->subjectPackageRepository->paginateWhereStudent($studentData);
        return $this->transformDataModInclude($subjectPackages, '', new ListPackagesTransformer([], $student->user),
            ResourceTypesEnums::SUBJECT_PACKAGE);
    }

}
