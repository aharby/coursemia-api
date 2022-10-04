<?php

namespace App\OurEdu\GeneralExamReport\SME\Controllers;

use App\OurEdu\GeneralExamReport\Repository\GeneralExamReportRepositoryInterface;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralExamReport\SME\Requests\ReportGeneralExamReportRequest;
use App\OurEdu\GeneralExamReport\SME\Transformers\GeneralExamReportQuestionsTransformer;
use App\OurEdu\GeneralExamReport\SME\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\GeneralExamReport\SME\Transformers\SubjectTransformer;
use App\OurEdu\GeneralExamReport\UseCases\ReportGeneralExamReportUseCase\ReportGeneralExamReportUseCaseInterface;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class GeneralExamReportApiController extends BaseApiController
{
    protected $generalExamReportRepository;
    protected $reportGeneralExamReportUseCase;
    protected $parserInterface;

    public function __construct(
        GeneralExamReportRepositoryInterface $generalExamReportRepository,
        ReportGeneralExamReportUseCaseInterface $reportGeneralExamReportUseCase,
        ParserInterface $parserInterface

    )
    {
        $this->generalExamReportRepository = $generalExamReportRepository;
        $this->reportGeneralExamReportUseCase = $reportGeneralExamReportUseCase;
        $this->parserInterface = $parserInterface;

        $this->middleware('auth:api');
        $this->middleware('type:sme');
    }

    //list subject where has reports
    public function listSubjects() {

        $subjects = $this->generalExamReportRepository->smeSubjectsHasGeneralExamsWithReports();
        return $this->transformDataModInclude($subjects, 'actions', new SubjectTransformer(), ResourceTypesEnums::SUBJECT);

    }

    public function listSubjectSections($subject) {

        $subject = $this->generalExamReportRepository->listReportedSubjectSections($subject);
        return $this->transformDataModInclude(
            $subject,
            'subjectFormatSubjects,questionReports',
            new SubjectTransformer(['details' => 1]),
            ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);

    }

    public function listSectionSections($section) {

        $section = $this->generalExamReportRepository->listReportedSectionSections($section);
        return $this->transformDataModInclude(
            $section,
            'subjectFormatSubjects,questionReports',
            new SubjectFormatSubjectTransformer(['details' => 1]),
            ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);
    }
    //list Subject Reported Questions where has (not reported not ignored) and has

    public function listSubjectReportedQuestions($subjectId) {

        $reports = $this->generalExamReportRepository->listGeneralExamReportedQuestions($subjectId);

        return $this->transformDataModInclude($reports, '', new GeneralExamReportQuestionsTransformer(), ResourceTypesEnums::GENERAL_EXAM_REPORT_QUESTION);

    }

    public function listGeneralExamReportedQuestions($subjectId) {

        $reports = $this->generalExamReportRepository->listGeneralExamReportedQuestions($subjectId);

        return $this->transformDataModInclude($reports, '', new GeneralExamReportQuestionsTransformer(), ResourceTypesEnums::GENERAL_EXAM_REPORT_QUESTION);

    }

    public function subjectReportedQuestionDetails($reportId) {

        $report = $this->generalExamReportRepository->generalExamReportQuestionDetails($reportId);

        return $this->transformDataModInclude($report, 'questionData', new GeneralExamReportQuestionsTransformer(['view_details' => true]), ResourceTypesEnums::GENERAL_EXAM_REPORT_QUESTION);

    }

    public function ignore($questionReportId) {

        $question = $this->generalExamReportRepository->findQuestionOrFail($questionReportId);
        if ($question->is_ignored) {
            return response()->json([
                'meta' => [
                    'message' => trans('app.Already Ignored Before')
                ]
            ]);
        }

        $this->generalExamReportRepository->ignoreQuestion($questionReportId);

        return response()->json([
            'meta' => [
                'message' => trans('app.Ignored Successfully')
            ]
        ]);
    }

    public function report($questionReportId , ReportGeneralExamReportRequest $request) {

        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $question = $this->generalExamReportRepository->findQuestionOrFail($questionReportId);
        if ($question->is_reported) {
            return response()->json([
                'meta' => [
                    'message' => trans('app.Already Reported Before')
                ]
            ]);
        }
        //Use Case Goes Here
        $user = Auth::user();

        $this->reportGeneralExamReportUseCase->report($questionReportId , $user , $data->note , $data->due_date);

        return response()->json([
            'meta' => [
                'message' => trans('app.Reported Successfully')
            ]
        ]);
    }

}
