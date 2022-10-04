<?php

namespace App\OurEdu\QuestionReport\SME\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\QuestionReport\Repository\QuestionReportRepository;
use App\OurEdu\QuestionReport\SME\Middleware\Api\IsAssigned;
use App\OurEdu\QuestionReport\SME\Requests\ReportQuestionReportRequest;
use App\OurEdu\QuestionReport\SME\Transformers\QuestionReportTransformer;
use App\OurEdu\QuestionReport\SME\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\QuestionReport\SME\Transformers\SubjectTransformer;
use App\OurEdu\QuestionReport\UseCases\ReportQuestionReportUseCase\ReportQuestionReportUseCase;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class QuestionReportController extends BaseApiController
{

    private $module;
    private $repository;
    private $reportQuestionReportUseCase;
    private $title;

    public function __construct(
        QuestionReportRepository $questionReportRepository,
        ReportQuestionReportUseCase $reportQuestionReportUseCase,
        ParserInterface $parserInterface
    ) {
        $this->repository = $questionReportRepository;
        $this->reportQuestionReportUseCase = $reportQuestionReportUseCase;
        $this->parserInterface = $parserInterface;
        $this->middleware('type:sme');
        $this->middleware(IsAssigned::class)->only('getQuestions');
    }

    public function getSubjectLists()
    {
        $subjects = $this->repository->smeSubjectsHasQuestionsReported();

        $include = 'actions';
        return $this->transformDataModInclude($subjects, $include, new SubjectTransformer(),
            ResourceTypesEnums::SUBJECT);
    }

    public function listSubjectSections($subject) {
        $subject = $this->repository->listReportedSubjectSections($subject);
        return $this->transformDataModInclude(
            $subject,
            'subjectFormatSubjects,questionReports',
            new SubjectTransformer(['details' => 1]),
            ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);
    }

    public function listSectionSections($section) {
        $section = $this->repository->listReportedSectionSections($section);
        return $this->transformDataModInclude(
            $section,
            'subjectFormatSubjects,questionReports',
            new SubjectFormatSubjectTransformer(['details' => 1]),
            ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);
    }

    public function getQuestions($subject) {

        $questions = $this->repository->all($subject);

        return $this->transformDataModInclude($questions, 'actions', new QuestionReportTransformer() , ResourceTypesEnums::QUESTIONS_REPORT);
    }

    public function viewQuestion($questionId)
    {
        $question = $this->repository->findOrFail($questionId);
        $include = 'questionData,actions';
        $filter['view_question'] = true;
        return $this->transformDataModIncludeItem($question, $include, new QuestionReportTransformer($filter),
            ResourceTypesEnums::QUESTIONS_REPORT);
    }

    public function ignoreQuestion($questionId)
    {
        $question = $this->repository->findOrFail($questionId);

        $questionReportRepo = new QuestionReportRepository($question);

        $questionReportRepo->ignore();

        return response()->json([
            'meta' => [
                'message' => trans('app.Ignored Successfully')
            ]
        ]);
    }


    public function reportQuestion($questionId , ReportQuestionReportRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();


        $user = Auth::user();
        $question = $this->repository->findOrFail($questionId);
        if ($question->is_reported) {
            return response()->json([
                'meta' => [
                    'message' => trans('app.Already Reported Before')
                ]
            ]);
        }

        $this->reportQuestionReportUseCase->report($questionId , $user , $data->note , $data->due_date);

        return response()->json([
            'meta' => [
                'message' => trans('app.Reported Successfully')
            ]
        ]);
    }
}
