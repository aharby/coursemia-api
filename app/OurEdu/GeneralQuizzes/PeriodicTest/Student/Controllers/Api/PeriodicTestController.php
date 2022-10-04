<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Student\Controllers\Api;

use App\OurEdu\GeneralQuizzes\PeriodicTest\Student\Requests\UpdatePeriodicTestTimeRequest;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Student\Transformers\FeedbackTransformer;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Student\Transformers\ListPeriodicTestTransformer;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Student\Transformers\QuestionBankTransformer;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Student\Transformers\whichStudentAnsweredTransformer;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\FinishGeneralQuizUseCase\FinishGeneralQuizUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\NextAndBack\GeneralQuizNextBackUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\StartGeneralQuizUseCase\StartGeneralQuizUseCaseInterface;
use App\OurEdu\SchoolAccounts\ClassroomClass\Repositories\ClassroomClassRepositoryInterface;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\GeneralQuizzes\UseCases\StudentPeriodicTestTimeUseCase\StudentPeriodicTestTimeUseCaseInterface;
class PeriodicTestController extends BaseApiController
{
    /**
     * @var ParserInterface
     */
    private $parserInterface;
    /**
     * @var GeneralQuizRepositoryInterface
     */
    private $generalQuizRepository;
    /**
     * @var StartGeneralQuizUseCaseInterface
     */
    private $startGeneralQuizUseCase;
    /**
     * @var PostAnswerUseCaseInterface
     */
    private $postAnswerUseCase;
    /**
     * @var GeneralQuizNextBackUseCaseInterface
     */
    private $nextBackUseCase;
    /**
     * @var FinishGeneralQuizUseCaseInterface
     */
    private $finishGeneralQuizUseCase;
    /**
     * @var ClassroomClassRepositoryInterface
     */
    private $classroomClassRepository;
    /**
     * @var GeneralQuizStudentRepositoryInterface
     */
    private $generalQuizStudentRepository;

    /**
     * @var StudentPeriodicTestTimeUseCaseInterface
     */
    private $studentPeriodicTestTimeUseCase;

    /**
     * @var array
     */
    private $filters;

    /**
     * PeriodicTestController constructor.
     * @param ParserInterface $parserInterface
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     * @param StartGeneralQuizUseCaseInterface $startGeneralQuizUseCase
     * @param PostAnswerUseCaseInterface $postAnswerUseCase
     * @param GeneralQuizNextBackUseCaseInterface $nextBackUseCase
     * @param FinishGeneralQuizUseCaseInterface $finishGeneralQuizUseCase
     * @param ClassroomClassRepositoryInterface $classroomClassRepository
     * @param GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository
     * @param StudentPeriodicTestTimeUseCaseInterface $studentPeriodicTestTimeUseCase
     */
    public function __construct(
        ParserInterface $parserInterface,
        GeneralQuizRepositoryInterface $generalQuizRepository,
        StartGeneralQuizUseCaseInterface $startGeneralQuizUseCase,
        PostAnswerUseCaseInterface $postAnswerUseCase,
        GeneralQuizNextBackUseCaseInterface $nextBackUseCase,
        FinishGeneralQuizUseCaseInterface $finishGeneralQuizUseCase,
        ClassroomClassRepositoryInterface $classroomClassRepository,
        GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository,
        StudentPeriodicTestTimeUseCaseInterface $studentPeriodicTestTimeUseCase
    )
    {
        $this->parserInterface = $parserInterface;
        $this->generalQuizRepository = $generalQuizRepository;
        $this->startGeneralQuizUseCase = $startGeneralQuizUseCase;
        $this->postAnswerUseCase = $postAnswerUseCase;
        $this->nextBackUseCase = $nextBackUseCase;
        $this->finishGeneralQuizUseCase = $finishGeneralQuizUseCase;
        $this->classroomClassRepository = $classroomClassRepository;
        $this->generalQuizStudentRepository = $generalQuizStudentRepository;
        $this->studentPeriodicTestTimeUseCase = $studentPeriodicTestTimeUseCase;
        $this->filters = [];
        // middleware
        $this->middleware('auth:api');
        $this->middleware('type:student');

    }

    public function list()
    {
        $this->setFilters();
        $periodicTests = $this->generalQuizRepository->listStudentAvailableGeneralQuizzes([GeneralQuizTypeEnum::PERIODIC_TEST,GeneralQuizTypeEnum::FORMATIVE_TEST],$this->filters);
        return $this->transformDataModInclude($periodicTests, 'actions,subject,sections', new ListPeriodicTestTransformer(), ResourceTypesEnums::Periodic_Test);
    }

    public function startPeriodicTest(GeneralQuiz $periodicTest)
    {
        $usecase = $this->startGeneralQuizUseCase->startQuiz($periodicTest->id);

        if ($usecase['status'] == 200) {
            $params["student"] = Auth::user();
            $bankQuestions = $usecase['bankQuestions'];
            $generalQuiz = $usecase['generalQuiz'];
            if (isset($usecase['last_question'])) {
                $params['finish_general_quiz'] = true;
            }
            $meta['message'] = $usecase['message'];

            return $this->transformDataModInclude($bankQuestions, 'questions', new QuestionBankTransformer($generalQuiz,$params), ResourceTypesEnums::Periodic_Test_QUESTION,$meta);
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function getNextOrBackQuestion(GeneralQuiz $periodicTest)
    {
        $page = request('page') ?? 1;
        $usecase = $this->nextBackUseCase->nextOrBackQuestion($periodicTest->id, $page);
        if ($usecase['status'] == 200) {
            $params["student"] = Auth::user();
            $bankQuestions = $usecase['bankQuestions'];
            $generalQuiz = $usecase['generalQuiz'];

            if (isset($usecase['last_question'])) {
                $params['finish_general_quiz'] = true;
            }
            return $this->transformDataModInclude($bankQuestions, 'questions', new QuestionBankTransformer($generalQuiz, $params), ResourceTypesEnums::Periodic_Test_QUESTION);
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function studentPeriodicTestTimeLeft(GeneralQuiz $periodicTest)
    {
        $useCase = $this->studentPeriodicTestTimeUseCase->getStudentPeriodicTestTimeLeft($periodicTest);
        return response()->json(
            [
                'meta' => [
                    'message' => trans('general_quizzes.Periodic Test time left for student'),
                    'student_test_time_left'=> (int) $useCase,
                ]
            ]
        );
    }


    public function updateStudentPeriodicTestTime(GeneralQuiz $periodicTest,UpdatePeriodicTestTimeRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $usecase = $this->studentPeriodicTestTimeUseCase->updateStudentPeriodicTestTime($periodicTest,$data);
        if ($usecase['status'] == 200) {
            return response()->json([
                'meta' => [
                    'message' => trans('general_quizzes.periodic test time updated')
                ]
            ]);
        } else {
            return formatErrorValidation($usecase);
        }
    }
    public function postAnswer(BaseApiRequest $request, GeneralQuiz $periodicTest)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $useCase = $this->postAnswerUseCase->postAnswer($periodicTest->id, $data);

        if ($useCase['status'] == 200) {
            return response()->json([
                'meta' => [
                    'message' => trans('exam.Answered successfully')
                ]
            ]);

        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function finishPeriodicTest(GeneralQuiz $periodicTest)
    {
        $studentId = auth()->user()->id;

        $usecase = $this->finishGeneralQuizUseCase->finishGeneralQuiz($periodicTest->id , $studentId);

        if ($usecase['status'] == 200) {

            $studentQuiz = $this->generalQuizStudentRepository->findStudentGeneralQuiz($periodicTest->id , $studentId);

            return $this->transformDataModIncludeItem($studentQuiz, "", new FeedbackTransformer(),ResourceTypesEnums::Periodic_Test_FEEDBACK);
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function feedback(GeneralQuiz $periodicTest)
    {
        $studentQuiz = $this->generalQuizStudentRepository->findStudentGeneralQuiz($periodicTest->id , \auth()->user()->id);

        if (!$studentQuiz) {
            unauthorize();
        }

        if ($periodicTest->end_at > now() and !$studentQuiz->is_finished) {
            $error= [
                "status" => 422,
                'title' => "quiz not finished yet",
                'detail' => "quiz not finished yet",
            ];

            return formatErrorValidation($error);
        }

        if (!$periodicTest->is_active) {
            $quizType = trans('general_quizzes.'.$periodicTest->quiz_type);
            $error= [
                "status" => 422,
                'title' => trans('general_quizzes.inactive general quiz',[
                    'quiz_type'=>$quizType
                ]),
                'detail' => $periodicTest->quiz_type.' is not active'
            ];

            return formatErrorValidation($error);
        }

        return $this->transformDataModIncludeItem($studentQuiz, "", new FeedbackTransformer(), ResourceTypesEnums::Periodic_Test_FEEDBACK);
    }


    public function whichStudentAnswered(GeneralQuiz $periodicTest)
    {
        $data = ['dum'=>'data'];

        return $this->transformDataModIncludeItem($data, "", new whichStudentAnsweredTransformer($periodicTest, Auth::user()),ResourceTypesEnums::WHICH_STUDENT_ANSWERED);
    }


    public function setFilters()
    {
        $subjectIds = array_unique($this->classroomClassRepository->getByClassroom(auth()->user()->student->classroom)->pluck('subject_id')->toArray());
        $subjects = Subject::query()->whereIn('id', $subjectIds)->pluck('id', 'name')->toArray();
        $this->filters[] = [
            'name' => 'subject_id',
            'type' => 'select',
            'data' => $subjects,
            'trans' => false,
            'value' => request()->get('subject_id'),
        ];
    }


}
