<?php

namespace App\OurEdu\Exams\Student\Controllers\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Events\PracticeEvents\StudentFinishedPractice;
use App\OurEdu\Exams\Events\PracticeEvents\StudentStartedPractice;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\Student\Middleware\Api\GenerateExamAndPracticeMiddleware;
use App\OurEdu\Exams\Student\Middleware\Api\PracticeMiddleware;
use App\OurEdu\Exams\Student\Requests\Practices\GeneratePracticeRequest;
use App\OurEdu\Exams\Student\Transformers\Practices\PracticeTransformer;
use App\OurEdu\Exams\Student\Transformers\Practices\QuestionTransformer;
use App\OurEdu\Exams\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\FinishExamUseCase\FinishExamUseCaseInterface;
use App\OurEdu\Exams\UseCases\HandelExamQuestionTimeUseCase\HandelExamQuestionTimeUseCaseInterface;
use App\OurEdu\Exams\UseCases\NextBackUseCase\NextBackUseCaseInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamUseCase\GenerateExamUseCaseInterface;
use App\OurEdu\Exams\UseCases\StartExamUseCase\StartExamUseCaseInterface;
use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use App\OurEdu\Subjects\Models\Subject;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use Throwable;
use function response;
use App\OurEdu\Exams\UseCases\RequestLiveSessionUseCase\RequestLiveSessionUseCaseInterface;

class PracticeApiController extends BaseApiController
{
    private $parserInterface;
    private $generateExamUseCaseInterface;
    private $postAnswerUserCaseInterface;
    private $startExamUseCase;
    private $finishExamUseCase;
    private $nextBackUseCase;
    private $examRepository;
    private $optionRepository;
    private $handelExamQuestionTimeUseCase;
    private $requestLiveSessionUseCase;
    private $filters = [];


    public function __construct(
        ParserInterface $parserInterface,
        GenerateExamUseCaseInterface $generateExamUseCaseInterface,
        PostAnswerUseCaseInterface $postAnswerUserCaseInterface,
        StartExamUseCaseInterface $startExUseCaseInterface,
        FinishExamUseCaseInterface $finishExamUseCaseInterface,
        NextBackUseCaseInterface $nextBackUseCaseInterface,
        ExamRepositoryInterface $examRepository,
        OptionRepositoryInterface $optionRepository,
        HandelExamQuestionTimeUseCaseInterface $handelExamQuestionTimeUseCase,
        RequestLiveSessionUseCaseInterface $requestLiveSessionUseCase

    ) {
        $this->parserInterface = $parserInterface;
        $this->generateExamUseCaseInterface = $generateExamUseCaseInterface;
        $this->postAnswerUserCaseInterface = $postAnswerUserCaseInterface;
        $this->startExamUseCase = $startExUseCaseInterface;
        $this->finishExamUseCase = $finishExamUseCaseInterface;
        $this->nextBackUseCase = $nextBackUseCaseInterface;
        $this->examRepository = $examRepository;
        $this->optionRepository = $optionRepository;
        $this->handelExamQuestionTimeUseCase = $handelExamQuestionTimeUseCase;
        $this->requestLiveSessionUseCase = $requestLiveSessionUseCase;

        $this->middleware(PracticeMiddleware::class)
            ->except(['generatePractice', 'listPractices']);
        $this->middleware(GenerateExamAndPracticeMiddleware::class)->only('generatePractice');
    }

    public function viewPractice(Request $request, $examId)
    {
        try {
            $exam = $this->examRepository->findOrFail($examId);
            $include = 'questions,feedback';

            $params['actions'] = false;

            return $this->transformDataModInclude($exam, $include, new PracticeTransformer($params), ResourceTypesEnums::Exam);
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function listPractices()
    {
        try {
            $this->setFilters();
            $studentId = auth()->user()->student->id;
            $exams = $this->examRepository->listPractices($studentId, $this->filters);

            $params['view_exam'] = true;

            $meta = [
            ];

            return $this->transformDataModInclude(
                $exams,
                'actions',
                new PracticeTransformer($params),
                ResourceTypesEnums::PRACTICE,
                $meta
            );
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function generatePractice(GeneratePracticeRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();


        try {
            DB::beginTransaction();
            $subjectFormatSubjectIds = $data->subject_format_subject_ids;
            $subjectId = $data->subject_id;

            $student = auth()->user()->student;
            $exam = $this->generateExamUseCaseInterface->generatePractice(
                $student,
                $subjectId,
                $subjectFormatSubjectIds
            );

            if (isset($exam['error'])) {

                return formatErrorValidation($exam);
            }
            DB::commit();

            $meta = ['message' => trans('api.Practice generated')];

            return $this->transformDataModInclude(
                $exam,
                'actions',
                new PracticeTransformer(),
                ResourceTypesEnums::Exam,
                $meta
            );
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
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
                    'is_answer' => true
                ];

                $meta = ['message' => trans('api.Answered successfully')];

                return $this->transformDataModInclude(
                    $questions,
                    'actions',
                    new QuestionTransformer($params),
                    ResourceTypesEnums::EXAM_QUESTION,
                    $meta
                );
            } else {
                return formatErrorValidation($nextOrBackQuestion);
            }
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function startPractice(int $examId)
    {
        $usecase = $this->startExamUseCase->startExam($examId);
        if ($usecase['status'] == 200) {
            $questions = $usecase['questions'];

            $params['next'] = $questions->nextPageUrl();

            $meta = ['message' => trans('api.Practice started')];
            if (isset($usecase['exam_data'])) {
                StudentStartedPractice::dispatch(
                    Arr::except($usecase['exam_data'], 'subject_id'),
                    Auth::user()->toArray(),
                    [
                        'subject_id' => $usecase['exam_data']['subject_id']
                    ]
                );
            }
            return $this->transformDataModInclude(
                $questions,
                'questions,actions',
                new QuestionTransformer($params),
                ResourceTypesEnums::EXAM_QUESTION,
                $meta
            );
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function finishPractice(int $examId)
    {
        try {
            $usecase = $this->finishExamUseCase->finishExam($examId);
            $exam = $this->examRepository->findOrFail($examId);

            if ($usecase['status'] == 200) {
                $meta = ['message' => trans('api.Practice is finished')];
                $meta = [
                    'message' => $usecase['message'],
                ];
                $includes = 'questions,feedback';

                $params['actions'] = false;

                $exam->vcrSpot =  $this->requestLiveSessionUseCase->getAvailableVcrSpot($exam->subject_id);
                if (isset($usecase['exam_data'])) {
                    StudentFinishedPractice::dispatch(
                        Arr::except($usecase['exam_data'], 'subject_id'),
                        Auth::user()->toArray(),
                        [
                            'subject_id' => $usecase['exam_data']['subject_id']
                        ]
                    );
                }
                return $this->transformDataModInclude(
                    $exam,
                    $includes,
                    new PracticeTransformer($params),
                    ResourceTypesEnums::Exam,
                    $meta
                );
            } else {
                return formatErrorValidation($usecase);
            }
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function getNextOrBackQuestion(int $examId, Request $request)
    {
        $page = request()->input('page') ?? 1;

        try {
            $nextOrBackQuestion = $this->nextBackUseCase->nextOrBackQuestion($examId, $page);

            if ($nextOrBackQuestion['status'] == 200) {
                $currentQuestionId = $request->current_question;
                $questions = $nextOrBackQuestion['questions'];
//


                $params['next'] = $questions->nextPageUrl();

                if ($page > 1) {
                    $params['previous'] = $questions->previousPageUrl();
                }

                return $this->transformDataModInclude(
                    $questions,
                    '',
                    new QuestionTransformer($params),
                    ResourceTypesEnums::EXAM_QUESTION
                );
            } else {
                return formatErrorValidation($nextOrBackQuestion);
            }
        } catch (Exception $exception) {
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
        }
    }

    /*
     *  This function fot setting the filters for listExam
     */
    public function setFilters()
    {
        $subjectIds = Exam::query()
            ->where('student_id', auth()->user()->student->id)
            ->where('type', ExamTypes::PRACTICE)
            ->pluck('subject_id')
            ->toArray();

        $subjects = Subject::query()
            ->whereIn('id', $subjectIds)
            ->pluck('id', 'name')
            ->toArray();

        $this->filters[] = [
            'name' => 'subject_id',
            'type' => 'select',
            'data' => $subjects,
            'trans' => false,
            'value' => request()->get('subject_id'),
        ];
    }
}
