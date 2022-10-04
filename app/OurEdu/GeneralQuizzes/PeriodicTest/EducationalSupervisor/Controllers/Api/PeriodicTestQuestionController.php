<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\EducationalSupervisor\Controllers\Api;


use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Middlewares\EducationalSupervisorMiddleware;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\PeriodicTest\EducationalSupervisor\Requests\PeriodicTestQuestionRequest;
use App\OurEdu\GeneralQuizzes\PeriodicTest\EducationalSupervisor\Transformers\QuestionBankTransformer;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Requests\AddQuestionsFromQuestionBankRequest;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionBankTransformer as TransformersQuestionBankTransformer;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\AddQuestionBankToGeneralQuizInterface;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralQuizQuestionUseCaseInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class PeriodicTestQuestionController extends BaseApiController
{
    /**
     * @var GeneralQuizQuestionUseCaseInterface
     */
    private $generalQuizQuestionUseCase;
    /**
     * @var GeneralQuizRepositoryInterface
     */
    private $generalQuizRepository;
    /**
     * @var ParserInterface
     */
    private $addQuestionBankToGeneralQuiz;

    private $parser;

    /**
     * PeriodicTestQuestionController constructor.
     * @param GeneralQuizQuestionUseCaseInterface $generalQuizQuestionUseCase
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     * * @param AddQuestionBankToGeneralQuizInterface $addQuestionBankToGeneralQuiz
     * @param ParserInterface $parser
     */
    public function __construct(
        GeneralQuizQuestionUseCaseInterface $generalQuizQuestionUseCase,
        GeneralQuizRepositoryInterface $generalQuizRepository,
        AddQuestionBankToGeneralQuizInterface $addQuestionBankToGeneralQuiz,
        ParserInterface $parser
    ) {
        $this->generalQuizQuestionUseCase = $generalQuizQuestionUseCase;
        $this->generalQuizRepository = $generalQuizRepository;
        $this->parser = $parser;
        $this->addQuestionBankToGeneralQuiz = $addQuestionBankToGeneralQuiz;

        $this->middleware('type:educational_supervisor');
        $this->middleware(EducationalSupervisorMiddleware::class)->only(
            ['store', 'delete', 'addQuestionBankToGeneralQuiz', 'questionBankList']
        );
    }

    public function list(GeneralQuiz $periodicTest)
    {
        $bankQuestions = $this->generalQuizRepository->getGeneralQuizQuestions($periodicTest);

        return $this->transformDataModInclude(
            $bankQuestions,
            'actions',
            new QuestionBankTransformer($periodicTest),
            ResourceTypesEnums::Periodic_Test_QUESTION
        );
    }

    public function view(GeneralQuiz $periodicTest, GeneralQuizQuestionBank $questionBank)
    {
        $params['viewQuestion'] = true;

        return $this->transformDataModIncludeItem(
            $questionBank,
            "actions",
            new QuestionBankTransformer($periodicTest, $params),
            ResourceTypesEnums::Periodic_Test_QUESTION
        );
    }

    public function store(PeriodicTestQuestionRequest $request, GeneralQuiz $periodicTest)
    {
        // add validations for edu
        if (count($periodicTest->studentsAnswered)) {
            return formatErrorValidation([
                'status' => 422,
                'title' => 'cant edit attended quiz',
                'detail' => trans('general_quizzes.cant edit attended general quiz')
            ], 422);
        }

        $data = $this->parser->deserialize($request->getContent())->getData();
        try {
            DB::beginTransaction();
            $questionData = $this->generalQuizQuestionUseCase->addQuestion($periodicTest, $data);
            if (isset($questionData['errors'])) {
                return formatErrorValidation($questionData['errors']);
            }
            $this->generalQuizRepository->updateGeneralQuizMark($periodicTest);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            $error = [
                'status' => 422,
                'title' => $exception->getMessage(),
                'detail' => $exception->getMessage()
            ];

            return formatErrorValidation($error);
        }
        $questionBank = $questionData->questionBank;
        $include = 'actions';

        return $this->transformDataModInclude(
            $questionBank,
            $include,
            new QuestionBankTransformer($periodicTest),
            ResourceTypesEnums::Periodic_Test_QUESTION
        );
    }


    public function addQuestionBankToGeneralQuiz(
        AddQuestionsFromQuestionBankRequest $request,
        GeneralQuiz $periodicTest
    ) {
        if (count($periodicTest->studentsAnswered)) {
            return formatErrorValidation([
                'status' => 422,
                'title' => 'cant edit attended quiz',
                'detail' => trans('general_quizzes.cant edit attended general quiz')
            ], 422);
        }
        $data = $this->parser->deserialize($request->getContent())->getData();

        $usecase = $this->addQuestionBankToGeneralQuiz->addQuestions($periodicTest, $data);
        $this->generalQuizRepository->updateGeneralQuizMark($periodicTest);
        if ($usecase['status'] == 200) {
            $meta = [
                'message' => $usecase['message'],
            ];
            return response()->json(['meta' => $meta], $usecase['status']);
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function questionBankList(Request $request, GeneralQuiz $periodicTest)
    {
        $questions = $this->questionBankRepository->getAvailableBankQuestion(
            $periodicTest,
            $request->get("public_status")
        );

        return $this->transformDataModInclude(
            $questions,
            'questionData',
            new TransformersQuestionBankTransformer($periodicTest),
            ResourceTypesEnums::GENERAL_QUIZ_QUESTION_BANK
        );
    }

}
