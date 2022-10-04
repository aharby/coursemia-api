<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Controllers\Api;


use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Requests\AddQuestionsFromQuestionBankRequest;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Requests\PeriodicTestQuestionRequest;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Requests\ReviewEssayQuestionRequest;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Transformers\QuestionBankTransformer;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\QuestionBankRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionBankTransformer as TransformersQuestionBankTransformer;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\AddQuestionBankToGeneralQuizInterface;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralQuizQuestionUseCaseInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class PeriodicTestQuestionController extends BaseApiController
{
    /**
     * @var GeneralQuizQuestionUseCaseInterface
     */
    private $generalQuizQuestionUseCase;
    /**
     * @var ParserInterface
     */
    private $parser;
    /**
     * @var QuestionBankRepositoryInterface
     */
    private $questionBankRepository;
    /**
     * @var GeneralQuizRepositoryInterface
     */
    private $generalQuizRepository;
    /**
     * @var AddQuestionBankToGeneralQuizInterface
     */
    private $addQuestionBankToGeneralQuiz;

    /**
     * PeriodicTestQuestionController constructor.
     * @param GeneralQuizQuestionUseCaseInterface $generalQuizQuestionUseCase
     * @param QuestionBankRepositoryInterface $questionBankRepository
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     * @param ParserInterface $parser
     * @param AddQuestionBankToGeneralQuizInterface $addQuestionBankToGeneralQuiz
     */
    public function __construct(
        GeneralQuizQuestionUseCaseInterface $generalQuizQuestionUseCase,
        QuestionBankRepositoryInterface $questionBankRepository,
        GeneralQuizRepositoryInterface $generalQuizRepository,
        ParserInterface $parser,
        AddQuestionBankToGeneralQuizInterface $addQuestionBankToGeneralQuiz
    ) {
        $this->generalQuizQuestionUseCase = $generalQuizQuestionUseCase;
        $this->parser = $parser;
        $this->questionBankRepository = $questionBankRepository;
        $this->generalQuizRepository = $generalQuizRepository;
        $this->addQuestionBankToGeneralQuiz = $addQuestionBankToGeneralQuiz;

        $this->middleware('type:school_instructor|school_admin');
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
        return $this->transformDataModIncludeItem(
            $questionBank,
            "actions",
            new QuestionBankTransformer($periodicTest),
            ResourceTypesEnums::Periodic_Test_QUESTION
        );
    }

    public function store(PeriodicTestQuestionRequest $request, GeneralQuiz $periodicTest)
    {
        if ($periodicTest->created_by !== auth()->user()->id) {
            unauthorize();
        }
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
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);

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

    public function reviewEssay(
        ReviewEssayQuestionRequest $request,
        GeneralQuiz $periodicTest,
        GeneralQuizStudentAnswer $answer
    ) {
        if ($periodicTest->created_by !== auth()->user()->id) {
            unauthorize();
        }
        $data = $this->parser->deserialize($request->getContent())->getData();

        $usecase = $this->generalQuizQuestionUseCase->reviewEssay($periodicTest, $answer, $data);
        if ($usecase['status'] == 200) {
            $meta = [
                'message' => $usecase['message'],
            ];
            return response()->json(['meta' => $meta], $usecase['status']);
        } else {
            return formatErrorValidation($usecase);
        }
    }


    public function addQuestionBankToGeneralQuiz(
        AddQuestionsFromQuestionBankRequest $request,
        GeneralQuiz $periodicTest
    ) {
        if ($periodicTest->created_by !== auth()->user()->id) {
            unauthorize();
        }
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


    public function delete(GeneralQuiz $periodicTest, GeneralQuizQuestionBank $question)
    {
        if (count($periodicTest->studentsAnswered)) {
            return formatErrorValidation([
                'status' => 422,
                'title' => 'cant edit attended quiz',
                'detail' => trans('general_quizzes.cant edit attended general quiz')
            ], 422);
        }

        if (!$question->generalQuiz->count()) {
            return abort(404);
        }

        $question->generalQuiz()->detach($periodicTest->id);
        $this->generalQuizRepository->updateGeneralQuizMark($periodicTest);

        return response()->json([
            'meta' => [
                'message' => trans('app.Deleted Successfully')
            ]
        ]);
    }

}
