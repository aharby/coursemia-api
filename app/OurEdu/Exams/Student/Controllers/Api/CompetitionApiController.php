<?php

namespace App\OurEdu\Exams\Student\Controllers\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Events\CompetitionEvents\StudentFinishedCompetition;
use App\OurEdu\Exams\Events\CompetitionEvents\StudentJoinedCompetition;
use App\OurEdu\Exams\Events\CompetitionEvents\StudentStartedCompetition;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\Student\Jobs\StudentFinishedCompetitionJob;
use App\OurEdu\Exams\Student\Middleware\Api\CompetitionMiddleware;
use App\OurEdu\Exams\Student\Middleware\Api\GenerateCompetitionMiddleware;
use App\OurEdu\Exams\Student\Middleware\Api\JoinCompetitionMiddleware;
use App\OurEdu\Exams\Student\Requests\Competitions\GenerateCompetitionRequest;
use App\OurEdu\Exams\Student\Transformers\Competitions\CompetitionQuestionTransformer;
use App\OurEdu\Exams\Student\Transformers\Competitions\CompetitionStudentTransformer;
use App\OurEdu\Exams\Student\Transformers\Competitions\CompetitionTransformer;
use App\OurEdu\Exams\Student\Transformers\Competitions\ListCompetitionTransformer;
use App\OurEdu\Exams\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCaseInterface;
use App\OurEdu\Exams\UseCases\FinishExamUseCase\FinishExamUseCaseInterface;
use App\OurEdu\Exams\UseCases\HandelExamQuestionTimeUseCase\HandelExamQuestionTimeUseCaseInterface;
use App\OurEdu\Exams\UseCases\NextBackUseCase\NextBackUseCaseInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamUseCase\GenerateExamUseCaseInterface;
use App\OurEdu\Exams\UseCases\StartExamUseCase\StartExamUseCaseInterface;
use App\OurEdu\LearningResources\Enums\DifficultlyLevelEnums;
use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use Throwable;

class CompetitionApiController extends BaseApiController
{
    private $parserInterface;
    private $generateExamUseCaseInterface;
    private $postAnswerUserCaseInterface;
    private $startExamUseCase;
    private $nextBackUseCase;
    private $examRepository;
    private $optionRepository;
    private $handelExamQuestionTimeUseCase;
    private $userRepository;
    private $filters = [];


    public function __construct(
        ParserInterface $parserInterface,
        GenerateExamUseCaseInterface $generateExamUseCaseInterface,
        PostAnswerUseCaseInterface $postAnswerUserCaseInterface,
        StartExamUseCaseInterface $startExUseCaseInterface,
        private FinishExamUseCaseInterface $finishExamUseCaseInterface,
        NextBackUseCaseInterface $nextBackUseCaseInterface,
        ExamRepositoryInterface $examRepository,
        OptionRepositoryInterface $optionRepository,
        HandelExamQuestionTimeUseCaseInterface $handelExamQuestionTimeUseCase ,
        UserRepositoryInterface $userRepository

    ) {
        $this->parserInterface = $parserInterface;
        $this->generateExamUseCaseInterface = $generateExamUseCaseInterface;
        $this->postAnswerUserCaseInterface = $postAnswerUserCaseInterface;
        $this->startExamUseCase = $startExUseCaseInterface;
        $this->nextBackUseCase = $nextBackUseCaseInterface;
        $this->examRepository = $examRepository;
        $this->optionRepository = $optionRepository;
        $this->handelExamQuestionTimeUseCase = $handelExamQuestionTimeUseCase;
        $this->userRepository = $userRepository;
        $this->middleware(CompetitionMiddleware::class)
            ->except(['generateCompetition', 'listCompetitions', 'joinCompetition']);
        $this->middleware(GenerateCompetitionMiddleware::class)
            ->only('generateCompetition');
        $this->middleware(JoinCompetitionMiddleware::class)
            ->only('joinCompetition');

    }

    public function viewCompetition(Request $request, $examId)
    {
        try {
            $exam = $this->examRepository->findOrFail($examId);
            $student =  auth()->user()->student;
            $students = $this->finishExamUseCaseInterface->getStudentBulkOrderInCompetition($exam, $student);
            $include = 'competitionStudents,competition_group_order.students,competitionUser,CompetitionOrderedStudents,questions';
            $params['actions'] = false;
            $params['students'] = $students;
            return $this->transformDataModInclude($exam, $include, new CompetitionTransformer($params), ResourceTypesEnums::Exam);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function viewCompetitionStudentFeedBack(Request $request, $examId , $userID)
    {
        try {
            $exam = $this->examRepository->findOrFail($examId);
            $studentID = $this->userRepository->findOrFail($userID)->student->id;
            $student = $exam->competitionStudents()->where('competition_student.student_id' , $studentID)->firstOrFail();
            $student->exam = $exam;
            $include = 'questions';
            $params['actions'] = false;
            return $this->transformDataModInclude($student, $include, new CompetitionStudentTransformer($exam, $params), ResourceTypesEnums::COMPETITION_STUDENT);
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function joinCompetition(Request $request, $examId)
    {
        try {
            $exam = $this->examRepository->findOrFail($examId);

            if ($exam->is_started == 1) {
                $return['status'] = 422;
                $return['detail'] = trans('api.The competition is already started');
                $return['title'] = 'The competition is already started';
                return formatErrorValidation($return);
            }

            $studentId = auth()->user()->student->id;

            $examRepo = new ExamRepository($exam);
            if (! $examRepo->checkIfStudentInCompetition($studentId)) {
                $examRepo->joinCompetition($studentId);
            }

          //  $questions = $examRepo->returnQuestion(1);

//            $params['next'] = $questions->nextPageUrl();

            $meta = ['message' => trans('api.Joined successfully')];

            $competitionData = [
                'exam_id' => $examId,
                'exam_title' => $exam->title,
                'difficulty_level' => $exam->difficulty_level,
                'questions_number' => $exam->questions_number,
            ];
            StudentJoinedCompetition::dispatch(
                $competitionData,
                Auth::user()->toArray(),
                [
                    'subject_id' => $exam->subject_id
                ]
            );
            return $this->transformDataModInclude(
                $exam,
                'actions',
                new CompetitionTransformer(),
                ResourceTypesEnums::COMPETITION,
                $meta
            );
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function listCompetitions()
    {
        try {
            $this->setFilters();
            $studentId = auth()->user()->student->id;
            $exams = $this->examRepository->listCompetitions($studentId,   $this->filters);
            $params['actions'] = true;
            $params['view_exam'] = true;
            $meta = [
                'filters' => formatFiltersForApi($this->filters)
            ];
            return $this->transformDataModInclude($exams, 'actions ,competitionStudents,competitionUser', new ListCompetitionTransformer($params), ResourceTypesEnums::COMPETITION, $meta);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function generateCompetition(GenerateCompetitionRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();


        try {
            DB::beginTransaction();
            $numberOfQuestions = $data->number_of_questions;
            $difficultyLevel = $data->difficulty_level;
            $subjectFormatSubjectIds = $data->subject_format_subject_ids;
            $subjectId = $data->subject_id;

            $student = auth()->user()->student;

            $params['joinCompetition'] = true;

            $exam = $this->generateExamUseCaseInterface->generateCompetition($student, $subjectId, $subjectFormatSubjectIds, $numberOfQuestions, $difficultyLevel);

            $params['actions'] = true;

            if (isset($exam['error'])) {

                return formatErrorValidation([
                    'status' => 422,
                    'title' => 'Competition not created',
                    'detail' => $exam['message']
                ]);
            }
            DB::commit();

            $meta = ['message' => trans('api.Generated successfully')];

            return $this->transformDataModInclude(
                $exam,
                'actions',
                new CompetitionTransformer($params),
                ResourceTypesEnums::COMPETITION,
                $meta
        );
        } catch (\Throwable $e) {
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

            return response()->json([
                'meta' => [
                    'message' => trans('exam.Answered successfully')
                ]
            ]);

//            $page = request()->input('page') ?? 1;
//            $nextOrBackQuestion = $this->nextBackUseCase->nextOrBackQuestion($examId, $page);
//
//
//            if ($nextOrBackQuestion['status'] == 200) {
//                $questions = $nextOrBackQuestion['questions'];
//
//                $params = [
////                    'is_answer' => true,
//                    'actions' => true
//                ];
//
//                $meta = ['message' => trans('api.Answered successfully')];
//
//                return $this->transformDataModInclude(
//                    $questions,
//                    'actions',
//                    new CompetitionQuestionTransformer($params),
//                    ResourceTypesEnums::COMPETITION_QUESTION,
//                    $meta
//                );
//            } else {
//                return formatErrorValidation($nextOrBackQuestion);
//            }
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function startCompetition(int $examId)
    {
        $exam = $this->examRepository->findOrFail($examId);
        if ($exam->is_started == 1) {
            $return['status'] = 422;
            $return['detail'] = trans('api.The competition is already started');
            $return['title'] = 'The competition is already started';
            return formatErrorValidation($return);
        }

        $usecase = $this->startExamUseCase->startExam($examId);
        if ($usecase['status'] == 200) {
            $questions = $usecase['questions'];

            $params['next'] = $questions->nextPageUrl();

            $meta = ['message' => trans('api.Competition started')];
            $params['actions'] = true;
            if ($usecase['exam_data']) {
                $usecase['exam_data']['exam_id'] = $examId;
                StudentStartedCompetition::dispatch(
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
                new CompetitionQuestionTransformer($params),
                ResourceTypesEnums::COMPETITION_QUESTION,
                $meta
        );
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function finishCompetition(int $examId)
    {
        try {
            $exam = $this->examRepository->findOrFail($examId);

            StudentFinishedCompetitionJob::dispatch($exam, \auth()->user()->student);
            $student =  auth()->user()->student;
            $students = $this->finishExamUseCaseInterface->getStudentBulkOrderInCompetition($exam, $student);
            $params['students'] = $students;
           
            $include = 'competitionStudents,competitionUser,competition_group_order.students,CompetitionOrderedStudents';

            $meta = ['message' => trans('api.Competition is over')];
            if ($exam) {
                StudentFinishedCompetition::dispatch(
                    [
                        'exam_title' => $exam->title,
                        'difficulty_level' => $exam->difficulty_level,
                        'questions_number' => $exam->questions_number,
                        'result' => $exam->result,
                    ],
                    Auth::user()->toArray(),
                    [
                        'subject_id' => $exam->subject_id
                    ]
                );
            }
            return $this->transformDataModInclude($exam, $include, new CompetitionTransformer($params), ResourceTypesEnums::Exam, $meta);
        } catch (\Throwable $e) {
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

                if (isset($nextOrBackQuestion['last_question'])) {
                    $params['finish_competition'] = true;
                }

                $params['actions'] = true;

                $params['next'] = $questions->nextPageUrl();
                return $this->transformDataModInclude(
                    $questions,
                    'competition',
                    new CompetitionQuestionTransformer($params),
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

    public function getFirstQuestion(int $examId)
    {
        $exam = $this->examRepository->findOrFail($examId);
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
            'actions' => true
        ];
        return $this->transformDataModInclude(
            $questions,
            'questions,actions',
            new CompetitionQuestionTransformer($params),
            ResourceTypesEnums::COMPETITION_QUESTION);
    }

    public function setFilters()
    {
        $subjectIds = Exam::where('student_id', auth()->user()->student->id)->pluck('subject_id')->toArray();

        $subjects = Subject::whereIn('id', $subjectIds)->pluck('id', 'name')->toArray();
        $this->filters[] = [
            'name' => 'difficulty_level',
            'type' => 'select',
            'data' => DifficultlyLevelEnums::availableDifficultlyLevel(),
            'trans' => false,
            'value' => request()->get('difficulty_level'),
        ];
        $this->filters[] = [
            'name' => 'subject_id',
            'type' => 'select',
            'data' => $subjects,
            'trans' => false,
            'value' => request()->get('subject_id'),
        ];
    }
}
