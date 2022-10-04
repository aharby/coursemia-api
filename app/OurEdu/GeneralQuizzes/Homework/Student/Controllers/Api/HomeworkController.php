<?php

namespace App\OurEdu\GeneralQuizzes\Homework\Student\Controllers\Api;
use App\OurEdu\GeneralQuizzes\Homework\Student\Transformers\FeedbackTransformer;
use App\OurEdu\GeneralQuizzes\Homework\Student\Transformers\whichStudentAnsweredTransformer;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use App\OurEdu\GeneralQuizzes\Homework\Student\Middleware\StudentHasHomework;
use App\OurEdu\GeneralQuizzes\Homework\Student\Middleware\ValidateHomeworkStartTime;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\UseCases\FinishGeneralQuizUseCase\FinishGeneralQuizUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Homework\Student\Transformers\ListHomeworksTransformer;
use App\OurEdu\GeneralQuizzes\UseCases\NextAndBack\GeneralQuizNextBackUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\StartGeneralQuizUseCase\StartGeneralQuizUseCaseInterface;
use App\OurEdu\GeneralQuizzes\UseCases\AnswerUseCase\PostAnswerUseCase\PostAnswerUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Homework\Student\Transformers\QuestionBankTransformer;
use App\OurEdu\SchoolAccounts\ClassroomClass\Repositories\ClassroomClassRepositoryInterface;
use App\OurEdu\Subjects\Models\Subject;
class HomeworkController extends BaseApiController
{
    private $parserInterface;
    private $generalQuizRepository;
    private $startGeneralQuizUseCase;
    private $postAnswerUseCase;
    protected $nextBackUseCase;
    protected $finishGeneralQuizUseCase;
    protected $classroomClassRepo;
    /**
     * @var GeneralQuizStudentRepositoryInterface
     */
    private $generalQuizStudentRepository;

    /**
     * HomeworkController constructor.
     * @param ParserInterface $parserInterface
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     * @param StartGeneralQuizUseCaseInterface $startGeneralQuizUseCase
     * @param PostAnswerUseCaseInterface $postAnswerUseCase
     * @param GeneralQuizNextBackUseCaseInterface $nextBackUseCase
     * @param FinishGeneralQuizUseCaseInterface $finishGeneralQuizUseCase
     * @param ClassroomClassRepositoryInterface $classroomClassRepo
     * @param GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository
     */
    public function __construct(
        ParserInterface $parserInterface,
        GeneralQuizRepositoryInterface $generalQuizRepository,
        StartGeneralQuizUseCaseInterface $startGeneralQuizUseCase,
        PostAnswerUseCaseInterface $postAnswerUseCase,
        GeneralQuizNextBackUseCaseInterface $nextBackUseCase,
        FinishGeneralQuizUseCaseInterface $finishGeneralQuizUseCase,
        ClassroomClassRepositoryInterface $classroomClassRepo,
        GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository
    ) {
        $this->parserInterface = $parserInterface;
        $this->generalQuizRepository = $generalQuizRepository;
        $this->startGeneralQuizUseCase = $startGeneralQuizUseCase;
        $this->postAnswerUseCase = $postAnswerUseCase;
        $this->nextBackUseCase = $nextBackUseCase;
        $this->finishGeneralQuizUseCase = $finishGeneralQuizUseCase;
        $this->classroomClassRepo = $classroomClassRepo;

        $this->middleware('auth:api');
        $this->middleware('type:student');
        $this->middleware(StudentHasHomework::class)
        ->only('startHomework','postAnswer','finishHomework','getNextOrBackQuestion');

        $this->middleware(ValidateHomeworkStartTime::class)->only('whichStudentAnswered');

        $this->generalQuizStudentRepository = $generalQuizStudentRepository;
    }
    public function listHomeworks()
    {
        $this->setFilters();
        $homeworks = $this->generalQuizRepository->listStudentAvailableGeneralQuizzes(GeneralQuizTypeEnum::HOMEWORK,$this->filters);
        return $this->transformDataModInclude($homeworks, 'actions,subject,sections', new ListHomeworksTransformer(), ResourceTypesEnums::HOMEWORK);
    }


    public function startHomework(int $homeworkId)
    {
        $usecase = $this->startGeneralQuizUseCase->startQuiz($homeworkId);
        if ($usecase['status'] == 200) {
            $params["student"] = Auth::user();
            $bankQuestions = $usecase['bankQuestions'];
            $homework = $usecase['generalQuiz'];
            if (isset($usecase['last_question'])) {
                $params['finish_general_quiz'] = true;
            }
            $meta['message'] = $usecase['message'];
            return $this->transformDataModInclude($bankQuestions, 'questions', new QuestionBankTransformer($homework,$params), ResourceTypesEnums::HOMEWORK_QUESTION,$meta);
        } else {
            return formatErrorValidation($usecase);
        }
    }



    public function getNextOrBackQuestion($homeworkId)
    {
        $page = request('page') ?? 1;
        $usecase = $this->nextBackUseCase->nextOrBackQuestion($homeworkId, $page);
        if ($usecase['status'] == 200) {
            $params["student"] = Auth::user();
            $bankQuestions = $usecase['bankQuestions'];
            $homework = $usecase['generalQuiz'];

            if (isset($usecase['last_question'])) {
                $params['finish_general_quiz'] = true;
            }
            return $this->transformDataModInclude($bankQuestions, 'questions', new QuestionBankTransformer($homework, $params), ResourceTypesEnums::HOMEWORK_QUESTION);
        } else {
            return formatErrorValidation($usecase);
        }
    }


    public function postAnswer(BaseApiRequest $request, $homeworkId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $usecase = $this->postAnswerUseCase->postAnswer($homeworkId, $data);

        if ($usecase['status'] == 200) {
            return response()->json([
                'meta' => [
                    'message' => trans('exam.Answered successfully')
                ]
            ]);

        } else {
            return formatErrorValidation($usecase);
        }
    }


    public function finishHomework($homeworkId) {

        $studentId = auth()->user()->id;

        $usecase = $this->finishGeneralQuizUseCase->finishGeneralQuiz($homeworkId , $studentId);

        if ($usecase['status'] == 200) {
            $meta = [
                'message' => $usecase['message'] ,
            ];

            $studentQuiz = $this->generalQuizStudentRepository->findStudentGeneralQuiz($homeworkId , $studentId);

            return $this->transformDataModIncludeItem($studentQuiz, "", new FeedbackTransformer(),ResourceTypesEnums::Homework_FEEDBACK);
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function feedback(GeneralQuiz $homework)
    {
        $studentQuiz = $this->generalQuizStudentRepository->findStudentGeneralQuiz($homework->id , \auth()->user()->id);

        if (!$studentQuiz) {
            unauthorize();
        }

        if ($homework->end_at > now() and !$studentQuiz->is_finished) {
            $error= [
                "status" => 422,
                'title' => "quiz not finished yet",
                'detail' => "quiz not finished yet",
            ];

            return formatErrorValidation($error);
        }

        if (!$homework->is_active) {
            $quizType = trans('general_quizzes.'.$homework->quiz_type);
            $error= [
                "status" => 422,
                'title' => trans('general_quizzes.inactive general quiz',[
                        'quiz_type'=>$quizType
                    ]),
                    'detail' => $homework->quiz_type.' is not active'
                ];

            return formatErrorValidation($error);
        }

        return $this->transformDataModIncludeItem($studentQuiz, "", new FeedbackTransformer(), ResourceTypesEnums::Homework_FEEDBACK);
    }

    public function setFilters()
    {
        $this->filters[] = [];
        if (isset(auth()->user()->student->classroom)) {
            $subjectIds = array_unique($this->classroomClassRepo->getByClassroom(auth()->user()->student->classroom)->pluck('subject_id')->toArray());
            $subjects = Subject::whereIn('id', $subjectIds)->pluck('id', 'name')->toArray();
            $this->filters[] = [
            'name' => 'subject_id',
            'type' => 'select',
            'data' => $subjects,
            'trans' => false,
            'value' => request()->get('subject_id'),
        ];
        }
    }

    public function whichStudentAnswered(GeneralQuiz $homework)
    {
        $data = ['dum'=>'data'];

        return $this->transformDataModIncludeItem($data, "", new whichStudentAnsweredTransformer($homework, Auth::user()),ResourceTypesEnums::WHICH_STUDENT_ANSWERED);
    }

}
