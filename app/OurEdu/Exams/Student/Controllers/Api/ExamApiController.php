<?php

namespace App\OurEdu\Exams\Student\Controllers\Api;

use App\OurEdu\Exams\Events\ExamEvents\StudentFinishedExam;
use App\OurEdu\Exams\Events\ExamEvents\StudentStartedExam;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\Exams\Student\Jobs\ChallengeFinishedJob;
use App\OurEdu\Exams\Student\Middleware\Api\ViewChallengeExamMiddleware;
use App\OurEdu\Exams\Student\Requests\DummyQuestionRequest;
use App\OurEdu\Exams\Student\Transformers\Dummy\DummyQuestionTransformer;
use App\OurEdu\Exams\UseCases\ExamChallengeUseCase\ExamChallengeUseCaseInterface;
use App\OurEdu\Exams\UseCases\ExamTakeLikeUseCase\ExamTakeLikeUseCaseInterface;
use App\OurEdu\Exams\UseCases\RequestLiveSessionUseCase\RequestLiveSessionUseCaseInterface;
use App\OurEdu\VCRSchedules\Repository\VCRScheduleRepositoryInterface;
use App\OurEdu\VCRSchedules\UseCases\VCRNotificationUseCase\VCRNotificationUseCaseInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Throwable;
use Illuminate\Http\Request;
use App\OurEdu\Exams\Models\Exam;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Subjects\Models\Subject;
use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use App\OurEdu\Exams\Enums\ExamTypes;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Exams\Student\Requests\GenerateExamRequest;
use App\OurEdu\Exams\Student\Transformers\ExamTransformer;
use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\LearningResources\Enums\DifficultlyLevelEnums;
use App\OurEdu\Exams\Student\Transformers\QuestionTransformer;
use App\OurEdu\Exams\Student\Middleware\Api\ExamAndPracticeMiddleware;
use App\OurEdu\Exams\UseCases\NextBackUseCase\NextBackUseCaseInterface;
use App\OurEdu\Exams\UseCases\StartExamUseCase\StartExamUseCaseInterface;
use App\OurEdu\Exams\UseCases\FinishExamUseCase\FinishExamUseCaseInterface;
use App\OurEdu\Exams\Student\Middleware\Api\GenerateExamAndPracticeMiddleware;
use App\OurEdu\Exams\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\FinishExamUseCase\NotifyParentsAboutExamResultUseCase;
use App\OurEdu\Exams\UseCases\HandelExamQuestionTimeUseCase\HandelExamQuestionTimeUseCaseInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamUseCase\GenerateExamUseCaseInterface;

class ExamApiController extends BaseApiController
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
    protected $requestLiveSessionUseCase;
    protected $examChallengeUseCase;
    protected $examTakeLikeUseCase;
    private $notificationUseCase;
    private $filters = [];
    private  $vCRScheduleRepository;


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
        RequestLiveSessionUseCaseInterface $requestLiveSessionUseCase,
        ExamChallengeUseCaseInterface $examChallengeUseCase,
        ExamTakeLikeUseCaseInterface $examTakeLikeUseCase,
        VCRNotificationUseCaseInterface $notificationUseCase,
        VCRScheduleRepositoryInterface $vCRScheduleRepository
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
        $this->examChallengeUseCase = $examChallengeUseCase;
        $this->examTakeLikeUseCase = $examTakeLikeUseCase;
        $this->notificationUseCase = $notificationUseCase;
        $this->vCRScheduleRepository = $vCRScheduleRepository;
        $this->middleware(ExamAndPracticeMiddleware::class)
            ->except(['generateExam', 'setFilters', 'listExams' , 'dummyQuestion' , 'challenge' , 'viewChallengeExam' , 'take']);
        $this->middleware(GenerateExamAndPracticeMiddleware::class)->only('generateExam');
        $this->middleware(ViewChallengeExamMiddleware::class)->only('viewChallengeExam');
    }

    public function viewExam($examId)
    {
        try {
            $exam = $this->examRepository->findOrFail($examId);
            $include = 'feedback,actions,questions,recommendation,instructors.vcrSpot.actions,instructors.vcrSpot.subject';
            if (!$exam->challenged()->count()){
                $include .=',challenges';
            }else{
                $include .=',challenged';
            }
            $params['retake_exam'] = true;
            $params['actions'] = false;
            $params['challenged'] = true;

            $exam->instructorsVCR = $this->vCRScheduleRepository->getAvailableVcrSpotInstructors($exam->subject);


            return $this->transformDataModInclude($exam, $include, new ExamTransformer($params), ResourceTypesEnums::Exam);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }
    public function viewChallengeExam($examId)
    {
        try {
            $exam = $this->examRepository->findOrFail($examId);
            $include = 'questions,feedback';

            $params['retake_exam'] = false;

            $params['actions'] = false;

            return $this->transformDataModInclude($exam, $include, new ExamTransformer($params), ResourceTypesEnums::Exam);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    /*
     * This function for listing Exams with filters like: difficulty level
     */
    public function listExams()
    {
        try {
            $this->setFilters();
            $studentId = auth()->user()->student->id;
            $exams = $this->examRepository->listPreviousExams($studentId, $this->filters);
            $params['view_exam'] = true;
            $meta = [
                'filters' => formatFiltersForApi($this->filters)
            ];
            return $this->transformDataModInclude($exams, 'actions', new ExamTransformer($params), ResourceTypesEnums::Exam, $meta);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    /*
     *  This function fot setting the filters for listExam
     */
    public function setFilters()
    {
        $subjectIds = Exam::where('student_id', auth()->user()->student->id)->pluck('subject_id')->toArray();

        $subjects = Subject::whereIn('id', $subjectIds)->pluck('id', 'name')->toArray();
        $this->filters[] = [
            'name' => 'difficulty_level',
            'type' => 'select',
            'data' => DifficultlyLevelEnums::availableDifficultlyLevel(),
            'trans' => false,
            'value' => request()->get('difficultly_level'),
        ];

        $this->filters[] = [
            'name' => 'subject_id',
            'type' => 'select',
            'data' => $subjects,
            'trans' => false,
            'value' => request()->get('subject_id'),
        ];

        $this->filters[] = [
            'name' => 'type',
            'type' => 'select',
            'data' => ExamTypes::examTypes(),
            'trans' => false,
            'value' => request()->get('type')
        ];
    }

    /*
         * This function for generating exam with specific criteria which is:
         *  $difficultyLevel
         *  $numberOfQuestions
         *  $subjectId
         *  $subjectFormatSubjectIds => which can be sections or other things
         *
         * business details in the use case
         *
         * return: Exam with url in the actions to start the exam
     */
    public function generateExam(GenerateExamRequest $request): array|JsonResponse
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        DB::beginTransaction();
        $numberOfQuestions = $data->number_of_questions;
        $difficultyLevel = $data->difficulty_level;
        $sectionIds = $data->subject_format_subject_ids;
        $subjectId = $data->subject_id;
        $student = auth()->user()->student;
        $exam = $this->generateExamUseCaseInterface->generateExam(
            $student,
            $subjectId,
            $sectionIds,
            $numberOfQuestions,
            $difficultyLevel
        );

        if (isset($exam['error'])) {
            DB::rollBack();
            $data = [
                'data' => (object)[],
                'meta' => [
                    'message' => $exam['message']
                ]
            ];
            return \response()->json($data);
        }

        DB::commit();

        $meta = ['message' => trans('api.Exam generated')];

        return $this->transformDataModInclude($exam, 'actions', new ExamTransformer(), ResourceTypesEnums::Exam, $meta);
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
//            $questionId = $data->getId();
            $exam = $this->postAnswerUserCaseInterface->postAnswer($examId, $data);
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
        $usecase = $this->startExamUseCase->startExam($examId);
        if ($usecase['status'] == 200) {
            $questions = $usecase['questions'];
            $this->handelExamQuestionTimeUseCase->insertStart($questions->first());

            $params['next'] = $questions->nextPageUrl();
            $params['previous'] = $questions->previousPageUrl();
            $params['is_exam'] = true;

            $meta = ['message' => trans('api.Exam started')];
            if (isset($usecase['exam_data'])) {
                StudentStartedExam::dispatch(
                    Arr::except($usecase['exam_data'], 'subject_id'),
                    Auth::user()->toArray(),
                    [
                        'subject_id' => $usecase['exam_data']['subject_id']
                    ]
                );
            }
            return $this->transformDataModInclude($questions, 'questions,actions', new QuestionTransformer($params), ResourceTypesEnums::EXAM_QUESTION, $meta);
        } else {
            return formatErrorValidation($usecase);
        }
    }
    /*
     * After Generate exam and start exam
     * This function for finishing the exam with receiving a link to report the exam and recommendations
     *
     * business details in the use case
     *
     */

    public function finishExam(int $examId)
    {
        try {
            $usecase = $this->finishExamUseCase->finishExam($examId);
            $exam = $this->examRepository->findOrFail($examId);

            if ($usecase['status'] == 200) {
                $this->handelExamQuestionTimeUseCase->endAllOpenQuestions($examId);


                $include = 'feedback,questions';
                if (!$exam->challenged()->count()){
                    $include .=',recommendation,instructors.vcrSpot.actions,instructors.vcrSpot.subject,challenges';
                }else{
                    $include .=',challenged';
                }
                $params['actions'] = false;
                $meta = ['message' => trans('api.Exam is over')];

               $exam->instructorsVCR = $this->vCRScheduleRepository->getAvailableVcrSpotInstructors($exam->subject);

                NotifyParentsAboutExamResultUseCase::dispatch($exam,Auth::guard('api')->user());

                if (isset($usecase['exam_data'])) {
                    if($exam->challenged()->count()) {
                        ChallengeFinishedJob::dispatch($exam);
                    }
                    $params['challenged'] = true;

                    StudentFinishedExam::dispatch(
                        Arr::except($usecase['exam_data'], 'subject_id'),
                        Auth::user()->toArray(),
                        [
                            'subject_id' => $usecase['exam_data']['subject_id']
                        ]
                    );
                }

                if (!is_null($exam->vcr_session_id)) {
                    $this->notificationUseCase->examFinishedNotification($exam);
                }

                return $this->transformDataModInclude($exam, $include, new ExamTransformer($params), ResourceTypesEnums::Exam, $meta);
            } else {
                return formatErrorValidation($usecase);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    /*
     * After Generate exam and start exam
     * This function for getting the next or previous question
     *
     * business details in the use case
     *
     */

    public function getNextOrBackQuestion(int $examId, Request $request)
    {
        $page = request()->input('page') ?? 1;
        try {
            $nextOrBackQuestion = $this->nextBackUseCase->nextOrBackQuestion($examId, $page);

            if ($nextOrBackQuestion['status'] == 200) {
                $currentQuestionId = $request->current_question;
                $questions = $nextOrBackQuestion['questions'];
//

                $this->handelExamQuestionTimeUseCase->handleTime($questions, $currentQuestionId);

                $params['next'] = $questions->nextPageUrl();
                $params['previous'] = $questions->previousPageUrl();
                $params['is_exam'] = true;

                return $this->transformDataModInclude(
                    $questions,
                    'exam.actions',
                    new QuestionTransformer($params),
                    ResourceTypesEnums::EXAM_QUESTION
                );
            } else {
                return formatErrorValidation($nextOrBackQuestion);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
        }
    }
    /*
     * After Generate exam, start exam and finish the exam
     * This function for generating exam with the same criteria of another exam
     *
     *  >>> takes the wanted examId and getting the criteria from it
     *     and uses the generateExam to create a new exam
     */

    public function retakeExam($examId)
    {
        $previousExam = $this->examRepository->findOrFail($examId);
        $difficulty_level_id = $this->optionRepository->getOptionIdBySlug($previousExam->difficulty_level);
        $subject_format_subject_ids = json_decode($previousExam->subject_format_subject_id);
        try {
            DB::beginTransaction();
            $numberOfQuestions = $previousExam->questions_number;
            $difficultyLevel = $difficulty_level_id;
            $subjectFormatSubjectIds = $subject_format_subject_ids;
            $subjectId = $previousExam->subject_id;
            $student = auth()->user()->student;

            $exam = $this->generateExamUseCaseInterface->generateExam($student, $subjectId, $subjectFormatSubjectIds, $numberOfQuestions, $difficultyLevel);

            if (isset($exam['error'])) {
                $data = [
                    'data' => (object) [],
                    'meta' => [
                        'message' => $exam['message']
                    ]
                ];
                return \response()->json($data);
            }
            DB::commit();

            $meta = ['message' => trans('api.Exam generated')];

            return $this->transformDataModInclude($exam, 'actions', new ExamTransformer(), ResourceTypesEnums::Exam, $meta);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function challenge($examId)
    {
        try {
            DB::beginTransaction();

            $student = auth()->user()->student;
            $useCase = $this->examChallengeUseCase->create($examId , $student->id);

            if ($useCase['status'] != 200) {
                return formatErrorValidation($useCase);
            }

            DB::commit();

            return $this->transformDataModInclude($useCase['exam'], 'actions', new ExamTransformer(), ResourceTypesEnums::Exam);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }
    public function take($examId)
    {
        try {
            DB::beginTransaction();

            $student = auth()->user()->student;
            $useCase = $this->examTakeLikeUseCase->create($examId , $student->id);

            if ($useCase['status'] != 200) {
                return formatErrorValidation($useCase);
            }

            DB::commit();

            return $this->transformDataModInclude($useCase['exam'], 'actions', new ExamTransformer(), ResourceTypesEnums::Exam);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function dummyQuestion(DummyQuestionRequest $request , $type) {

        $question = new ExamQuestion();
        $question->slug = $type;
        return $this->transformDataModInclude($question, '', new DummyQuestionTransformer(), ResourceTypesEnums::EXAM_QUESTION);

    }
}
