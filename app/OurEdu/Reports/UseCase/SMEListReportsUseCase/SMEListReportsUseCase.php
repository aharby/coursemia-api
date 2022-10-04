<?php

namespace App\OurEdu\Reports\UseCase\SMEListReportsUseCase;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Reports\Repository\ReportRepositoryInterface;

class SMEListReportsUseCase implements SMEListReportsUseCaseInterface
{
    private $reportRepository;
    private $subjectRepository;
    private $user;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        SubjectRepositoryInterface $subjectRepository)
    {
        $this->reportRepository = $reportRepository;
        $this->subjectRepository = $subjectRepository;
        $this->user = Auth::guard('api')->user();
    }

    /*
     * this UseCase handle 3 scenarios:
     *      1- if $subjectId is null => get all the reported subjects or the subjects that hold
     *              a reported sections or resources
     *         else get the subjectReports with the necessarily Actions(on the Transformer)
     *
     *      2- if $subjectId and $subjectFormatSubjectId is value => get the parent subjectFormatSubject
     *         for it and
     *          all reports on this subjectFormatSubject with the necessarily Actions(on the Transformer)
     *
     *      3- if $subjectId, $subjectFormatSubjectId and $resourceSubjectFormatId is values => get the reports
     *          on this resources with the necessarily Actions(on the Transformer)
     * */
    public function listReports($subjectId,
                                $subjectFormatSubjectId,
                                $resourceId,
                                $reportedSections,
                                $reportedResources)
    {
        $returnedArr = [];
        if (!is_null($subjectId)) {
            if ($reportedSections == 'true') {
                $returnedArr['subject_reported_sections'] = $this->reportRepository->paginateSubjectSectionsReportsForSME($subjectId);
                $returnedArr['code'] = 200;
                return $returnedArr;
            }
            $returnedArr['subject_reports'] = $this->reportRepository->paginateSubjectReportsForSME($subjectId);
            $returnedArr['code'] = 200;
            return $returnedArr;
        }

        if (!is_null($subjectFormatSubjectId)) {
            if ($reportedResources == 'true') {
                $returnedArr['section_reported_resources'] = $this->reportRepository->paginateSectionResourcesReportsForSME($subjectFormatSubjectId);
                $returnedArr['code'] = 200;
                return $returnedArr;
            }
            $returnedArr['section_reports'] = $this->reportRepository->paginateSectionReportsForSME($subjectFormatSubjectId);
            $returnedArr['code'] = 200;
            return $returnedArr;
        }

        if (!is_null($resourceId)) {
            $returnedArr['resource_reports'] = $this->reportRepository->paginateResourcesReportsForSME($resourceId);
            $returnedArr['code'] = 200;
            return $returnedArr;
        }

        $smeSubjectsIds = $this->user->managedSubjects->pluck('id')->toArray();
        $returnedArr['all_reports'] = $this->reportRepository->paginateAllReportsForSME($smeSubjectsIds);
        $returnedArr['code'] = 200;
        return $returnedArr;
    }


}
