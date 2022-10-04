<?php

namespace App\OurEdu\Exams\Student\Controllers\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\Student\Middleware\Api\JoinCompetitionMiddleware;
use App\OurEdu\Exams\Student\Transformers\Competitions\CompetitionQuestionTransformer;
use App\OurEdu\Exams\Student\Transformers\InstructorCompetitions\InstructorCompetitionQuestionTransformer;
use App\OurEdu\Exams\Student\Transformers\InstructorCompetitions\InstructorCompetitionTransformer;
use App\OurEdu\Exams\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\HandelExamQuestionTimeUseCase\HandelExamQuestionTimeUseCaseInterface;
use App\OurEdu\Exams\UseCases\NextBackUseCase\NextBackUseCaseInterface;
use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class InstructorCompetitionApiController extends BaseApiController
{
    private $parserInterface;
    private $postAnswerUserCaseInterface;
    private $nextBackUseCase;
    private $examRepository;
    private $optionRepository;
    private $handelExamQuestionTimeUseCase;


    public function __construct(
        ParserInterface $parserInterface,
        PostAnswerUseCaseInterface $postAnswerUserCaseInterface,
        NextBackUseCaseInterface $nextBackUseCaseInterface,
        ExamRepositoryInterface $examRepository,
        OptionRepositoryInterface $optionRepository,
        HandelExamQuestionTimeUseCaseInterface $handelExamQuestionTimeUseCase
    ) {
        $this->parserInterface = $parserInterface;
        $this->postAnswerUserCaseInterface = $postAnswerUserCaseInterface;
        $this->nextBackUseCase = $nextBackUseCaseInterface;
        $this->examRepository = $examRepository;
        $this->optionRepository = $optionRepository;
        $this->handelExamQuestionTimeUseCase = $handelExamQuestionTimeUseCase;
        $this->middleware(JoinCompetitionMiddleware::class)
            ->only('joinInstructorCompetition');
    }

    public function joinInstructorCompetition($competitionId)
    {
        try {
            $exam = $this->examRepository->findOrFail($competitionId);

            if ($exam->is_started == 1) {
                $return['status'] = 422;
                $return['detail'] = trans('api.The competition is already started');
                $return['title'] = 'The competition is already started';
                return formatErrorValidation($return);
            }

            $studentId = auth()->user()->student->id;

            $examRepo = new ExamRepository($exam);
            if (! $examRepo->checkIfStudentInInstructorCompetition($studentId)) {
                $examRepo->joinInstructorCompetition($studentId);
            }

            $meta = ['message' => trans('api.Joined successfully')];
            return $this->transformDataModInclude(
                $exam,
                '',
                new InstructorCompetitionTransformer(),
                ResourceTypesEnums::INSTRUCTOR_COMPETITION,
                $meta
            );
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function getFirstQuestion(int $competitionId)
    {
        $exam = $this->examRepository->findOrFail($competitionId);
        if ($exam->is_started == 0) {
            $return['status'] = 422;
            $return['detail'] = trans('api.The competition is not started yet');
            $return['title'] = 'The competition is not started yet';
            return formatErrorValidation($return);
        }
        $examRepo = new ExamRepository($exam);
        $questions = $examRepo->returnQuestion(1);
        $params = [
            'next' => $questions->nextPageUrl(),
            'enable_actions' => true
        ];
        return $this->transformDataModInclude(
            $questions,
            'competition',
            new InstructorCompetitionQuestionTransformer($params),
            ResourceTypesEnums::COMPETITION_QUESTION
        );
    }

    public function getNextOrBackQuestion(int $examId, Request $request)
    {
        $page = request()->input('page') ?? 1;
        try {
            $nextOrBackQuestion = $this->nextBackUseCase->nextOrBackQuestion($examId, $page);

            if ($nextOrBackQuestion['status'] == 200) {
                $currentQuestionId = $request->current_question;
                $questions = $nextOrBackQuestion['questions'];

                $params['enable_actions'] = true;

                $params['next'] = $questions->nextPageUrl();
                return $this->transformDataModInclude(
                    $questions,
                    'competition',
                    new InstructorCompetitionQuestionTransformer($params),
                    ResourceTypesEnums::COMPETITION_QUESTION
                );
            } else {
                return formatErrorValidation($nextOrBackQuestion);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
        }
    }

    public function postAnswer(BaseApiRequest $request, $examId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        try {
            DB::beginTransaction();
            $this->postAnswerUserCaseInterface->postAnswer($examId, $data);
            DB::commit();
            $page = request()->input('page') ?? 1;
            $nextOrBackQuestion = $this->nextBackUseCase->nextOrBackQuestion($examId, $page);

            if ($nextOrBackQuestion['status'] == 200) {
                $questions = $nextOrBackQuestion['questions'];

                $params = [
                    'is_answer' => true,
                    'enable_actions' => true
                ];

                $meta = ['message' => trans('api.Answered successfully')];

                return $this->transformDataModInclude(
                    $questions,
                    '',
                    new InstructorCompetitionQuestionTransformer($params),
                    ResourceTypesEnums::COMPETITION_QUESTION,
                    $meta
                );
            } else {
                return formatErrorValidation($nextOrBackQuestion);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }
}
