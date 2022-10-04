<?php

namespace App\OurEdu\Reports\SME\Controllers\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Reports\ReportEnum;
use App\OurEdu\Reports\Repository\ReportRepositoryInterface;
use App\OurEdu\Reports\SME\Transformers\ListAllReportsTransformer;
use App\OurEdu\Reports\SME\Transformers\ListResourceReportsTransformer;
use App\OurEdu\Reports\SME\Transformers\ListSectionReportedResourcesTransformer;
use App\OurEdu\Reports\SME\Transformers\ListSectionReportsTransformer;
use App\OurEdu\Reports\SME\Transformers\ListSubjectReportedSectionsTransformer;
use App\OurEdu\Reports\SME\Transformers\ListSubjectReportsTransformer;
use App\OurEdu\Reports\SME\Transformers\ReportTransformer;
use App\OurEdu\Reports\SME\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\Reports\SME\Transformers\SubjectTransformer;
use App\OurEdu\Reports\UseCase\SMEListReportsUseCase\SMEListReportsUseCaseInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Subjects\Student\Transformers\ListSubjectsTransformer;
use Illuminate\Support\Facades\Log;

class ReportApiController extends BaseApiController
{
    private $reportRepository;
    private $subjectRepository;
    private $SMEListReportsUseCase;
    public  $params;

    public function __construct(
        SMEListReportsUseCaseInterface $SMEListReportsUseCase,
        ReportRepositoryInterface $reportRepository,
        SubjectRepositoryInterface $subjectRepository
    ) {
        $this->reportRepository = $reportRepository;
        $this->SMEListReportsUseCase = $SMEListReportsUseCase;
        $this->subjectRepository = $subjectRepository;
        $this->params = [];
    }

    public function listReports()
    {
        try{
            $subjectId = request()->input('subject_id') ?? null;
            $subjectFormatSubjectId = request()->input('section_id') ?? null;
            $resourceId = request()->input('resource_id') ?? null;
            $reportedSections = request()->input('reported_sections') ?? 'false';
            $reportedResources = request()->input('reported_resources') ?? 'false';
            $returnedArr = $this
                            ->SMEListReportsUseCase->listReports(
                                $subjectId,
                                $subjectFormatSubjectId,
                                $resourceId,
                                $reportedSections,
                                $reportedResources);

            if ($returnedArr['code'] == 200) {
                // case subject_reports
                if (isset($returnedArr['subject_reports'])) {
                    $include = '';
                    return $this->transformDataModInclude(
                        $returnedArr['subject_reports'],
                        $include,
                        new ListSubjectReportsTransformer(),
                        ResourceTypesEnums::REPORT);
                }
                // case subject_reported_sections
                if (isset($returnedArr['subject_reported_sections'])) {
                    $include = '';
                    return $this->transformDataModInclude(
                        $returnedArr['subject_reported_sections'],
                        $include,
                        new ListSubjectReportedSectionsTransformer(),
                        ResourceTypesEnums::REPORT);
                }
                // case section_reported_resources
                if (isset($returnedArr['section_reported_resources'])) {
                    $include = '';
                    return $this->transformDataModInclude(
                        $returnedArr['section_reported_resources'],
                        $include,
                        new ListSectionReportedResourcesTransformer(),
                        ResourceTypesEnums::REPORT);
                }
                // case section_reports
                if (isset($returnedArr['section_reports'])) {
                    $include = '';
                    return $this->transformDataModInclude(
                        $returnedArr['section_reports'],
                        $include,
                        new ListSectionReportsTransformer(),
                        ResourceTypesEnums::REPORT);
                }
                // case resource_reports
                if (isset($returnedArr['resource_reports'])) {
                    $include = '';
                    return $this->transformDataModInclude(
                        $returnedArr['resource_reports'],
                        $include,
                        new ListResourceReportsTransformer(),
                        ResourceTypesEnums::REPORT);
                }
                // case all_reports
                if (isset($returnedArr['all_reports'])) {
                    $include = '';
                    return $this->transformDataModInclude(
                        $returnedArr['all_reports'],
                        $include,
                        new ListAllReportsTransformer(),
                        ResourceTypesEnums::REPORT);
                }
            } else {
                $error = [
                    'status' => $returnedArr['code'],
                    'title' => $returnedArr['title'],
                    'detail' => $returnedArr['detail']
                ];
                return formatErrorValidation($error, $returnedArr['code']);
            }
        }catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }

    }

//    public function viewReport($reportId)
//    {
//        try{
//            $report = $this->reportRepository->findOrFail($reportId);
//            switch ($report->reportable_type) {
//                case $report->reportable_type == ReportEnum::SUBJECT_MODEL:
//                    $this->params['subject'] = true;
//                    break;
//                case $report->reportable_type == ReportEnum::SUBJECT_FORMAT_SUBJECT_MODEL:
//                    $this->params['subjectFormatSubject'] = true;
//                    break;
//                case $report->reportable_type == ReportEnum::RESOURCE_SUBJECT_FORMAT_SUBJECT_MODEL:
//                    $this->params['resourceSubjectFormatSubject'] = true;
//                    break;
//            }
//            return $this->transformDataModInclude($report, '', new ReportTransformer($this->params), ResourceTypesEnums::REPORT);
//        }catch (\Throwable $e) {
//            Log::error($e);
//            throw new OurEduErrorException($e->getMessage());
//        }
//    }

    public function listSubjects() {
       $subjects = $this->reportRepository->listReportedSubjects();

        return $this->transformDataModInclude(
            $subjects,
            '',
            new SubjectTransformer(),
            ResourceTypesEnums::SUBJECT);
    }

    public function listSubjectSections($subject) {
        $sections = $this->reportRepository->listReportedSubjectSections($subject);
        return $this->transformDataModInclude(
            $sections,
            'subjectFormatSubjects',
            new SubjectTransformer(['details' => 1]),
            ResourceTypesEnums::SUBJECT);
    }

    public function listSectionSections($section) {
        $section = $this->reportRepository->listReportedSectionSections($section);
        return $this->transformDataModInclude(
            $section,
            'subjectFormatSubjects',
            new SubjectFormatSubjectTransformer(['details' => 1]),
            ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);
    }


}
