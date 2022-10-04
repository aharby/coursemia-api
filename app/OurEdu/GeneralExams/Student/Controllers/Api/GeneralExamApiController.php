<?php

namespace App\OurEdu\GeneralExams\Student\Controllers\Api;

use App\OurEdu\GeneralExams\UseCases\FinishGeneralExamUseCase\FinishGeneralExamUseCaseInterface;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\GeneralExams\Student\Transformers\GeneralExamTransformer;
use App\OurEdu\GeneralExams\Student\Transformers\GeneralExamQuestionTransformer;
use App\OurEdu\GeneralExams\Repository\GeneralExam\GeneralExamRepositoryInterface;
use App\OurEdu\GeneralExams\UseCases\NextAndBack\GeneralExamNextBackUseCaseInterface;
use App\OurEdu\GeneralExams\UseCases\StartExamUseCase\StartGeneralExamUseCaseInterface;
use App\OurEdu\GeneralExams\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCaseInterface;

class GeneralExamApiController extends BaseApiController
{
    private $parserInterface;
    private $generalExamRepository;
    private $startGeneralExamUseCase;
    private $postAnswerUseCase;
    protected $nextBackUseCase;
    protected $finishGeneralExamUseCase;


    public function __construct(
        ParserInterface $parserInterface,
        GeneralExamRepositoryInterface $generalExamRepository,
        StartGeneralExamUseCaseInterface $startGeneralExamUseCase,
        PostAnswerUseCaseInterface $postAnswerUseCase,
        GeneralExamNextBackUseCaseInterface $nextBackUseCase,
        FinishGeneralExamUseCaseInterface $finishGeneralExamUseCase
    ) {
        $this->parserInterface = $parserInterface;
        $this->generalExamRepository = $generalExamRepository;
        $this->startGeneralExamUseCase = $startGeneralExamUseCase;
        $this->postAnswerUseCase = $postAnswerUseCase;
        $this->nextBackUseCase = $nextBackUseCase;
        $this->finishGeneralExamUseCase = $finishGeneralExamUseCase;

        $this->middleware('auth:api');
        $this->middleware('type:student');
    }

    /*
     * This function for listing Available General Exams
     */
    public function listExams($subjectId)
    {
        try {
            $studentId = auth()->user()->student->id;
            $exams = $this->generalExamRepository->listStudentAvailableExams($subjectId);

            return $this->transformDataModInclude($exams, 'actions,subject', new GeneralExamTransformer(), ResourceTypesEnums::GENERAL_EXAM);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    /*
     * After Generating exam
     * This function for starting the exam and getting the first question
     *
     * business details in the use case
     *
     * return: question with url in the actions to for the next question the exam
     */
    public function startExam(int $examId)
    {
        $studentId = auth()->user()->student->id;

        $usecase = $this->startGeneralExamUseCase->startExam($examId, $studentId);

        if ($usecase['status'] == 200) {
            $questions = $usecase['questions'];
            $params['next'] = $questions->nextPageUrl();
            $params['previous'] = $questions->previousPageUrl();
            $params['is_exam'] = true;

            $meta = ['message' => trans('api.Exam started')];

            return $this->transformDataModInclude($questions, 'actions,exam', new GeneralExamQuestionTransformer($params), ResourceTypesEnums::GENERAL_EXAM_QUESTION, $meta);
        } else {
            return formatErrorValidation($usecase);
        }
    }

    /*
     * This function for posting the questions (in the Exam) answers
     *
     * business details in the use case
     *
     * return: message
     */
    public function postAnswer(BaseApiRequest $request, $examId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        try {
            DB::beginTransaction();
            $exam = $this->postAnswerUseCase->postAnswer($examId, $data);
            DB::commit();

            return response()->json([
                'meta' => [
                    'message' => trans('exam.Answered successfully')
                ]
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function getNextOrBackQuestion($examId)
    {
        $page = request('page') ?? 1;

        $questions = $this->nextBackUseCase->nextOrBackQuestion($examId, $page);

        $params = [
            'next' =>  $questions->nextPageUrl(),
            'previous' =>  $questions->previousPageUrl(),
        ];

        return $this->transformDataModInclude(
            $questions,
            ['exam.actions', 'actions', 'options'],
            new GeneralExamQuestionTransformer($params),
            ResourceTypesEnums::GENERAL_EXAM_QUESTION
        );
    }

    public function finishExam($examId) {

        $student = auth()->user()->student;

        $response = $this->finishGeneralExamUseCase->finishExam($examId , $student->id);

        if ($response['status'] == 200) {
            $meta = [
                'message' => $response['message'] ,
            ];
        } else {
            $meta['detail'] = $response['detail'];
            $meta['title'] = $response['title'];
        }

        return response()->json(['meta'=>$meta], $response['status']);
    }
}
