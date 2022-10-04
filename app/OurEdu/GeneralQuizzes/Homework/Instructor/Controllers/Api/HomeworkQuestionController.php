<?php


namespace App\OurEdu\GeneralQuizzes\Homework\Instructor\Controllers\Api;


use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Requests\AddQuestionsFromQuestionBankRequest;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Requests\HomeWorkQuestionRequest;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Requests\ReviewEssayQuestionRequest;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\QuestionBankTransformer;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\QuestionBankRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionBankTransformer as ListBankQuestionsTransformer;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\AddQuestionBankToGeneralQuizInterface;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralQuizQuestionUseCaseInterface;
use App\OurEdu\Users\UserEnums;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class HomeworkQuestionController extends BaseApiController
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
     * HomeWorkQuestionController constructor.
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

        $this->middleware('type:school_instructor|school_supervisor|academic_coordinator|school_leader|school_admin');
    }

    public function list(GeneralQuiz $homework)
    {
        $bankQuestions = $this->generalQuizRepository->getGeneralQuizQuestions($homework);

        $questions = [];

        /*foreach ($bankQuestions as $question) {
            if (isset($question->questions)) {
                $questions[] = $question->questions;
            }
        }*/
        //return $this->transformDataModInclude($questions, '', new QuestionTransformer($homework), ResourceTypesEnums::HOMEWORK_QUESTION_DATA);
        return $this->transformDataModInclude(
            $bankQuestions,
            'actions',
            new QuestionBankTransformer($homework),
            ResourceTypesEnums::HOMEWORK_QUESTION
        );
    }

    public function view(GeneralQuiz $homework, GeneralQuizQuestionBank $questionBank)
    {
        return $this->transformDataModIncludeItem(
            $questionBank,
            "actions",
            new QuestionBankTransformer($homework),
            ResourceTypesEnums::HOMEWORK_QUESTION
        );
    }

    public function store(HomeWorkQuestionRequest $request, GeneralQuiz $homework)
    {
        if ($homework->created_by !== auth()->user()->id && !in_array(\auth()->user()->type, [
                UserEnums::SCHOOL_SUPERVISOR,
                UserEnums::SCHOOL_LEADER,
                UserEnums::ACADEMIC_COORDINATOR
            ])) {
            unauthorize();
        }
        if (count($homework->studentsAnswered)) {
            return formatErrorValidation([
                'status' => 422,
                'title' => 'cant edit attended homework',
                'detail' => trans('general_quizzes.cant edit attended general quiz')
            ], 422);
        }
        $data = $this->parser->deserialize($request->getContent())->getData();
        try {
            DB::beginTransaction();
            $questionData = $this->generalQuizQuestionUseCase->addQuestion($homework, $data);
            if (isset($questionData['errors'])) {
                return formatErrorValidation($questionData['errors']);
            }
            $this->generalQuizRepository->updateGeneralQuizMark($homework);
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
            new QuestionBankTransformer($homework),
            ResourceTypesEnums::HOMEWORK_QUESTION
        );
    }

    public function reviewEssay(
        ReviewEssayQuestionRequest $request,
        GeneralQuiz $homework,
        GeneralQuizStudentAnswer $answer
    ) {
        if ($homework->created_by !== auth()->user()->id) {
            unauthorize();
        }
        $data = $this->parser->deserialize($request->getContent())->getData();

        $usecase = $this->generalQuizQuestionUseCase->reviewEssay($homework, $answer, $data);
        if ($usecase['status'] == 200) {
            $meta = [
                'message' => $usecase['message'],
            ];
            return response()->json(['meta' => $meta], $usecase['status']);
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function addQuestionBankToGeneralQuiz(AddQuestionsFromQuestionBankRequest $request, GeneralQuiz $homework)
    {
        if ($homework->created_by !== auth()->user()->id) {
            unauthorize();
        }
        if (count($homework->studentsAnswered)) {
            return formatErrorValidation([
                'status' => 422,
                'title' => 'cant edit attended homework',
                'detail' => trans('general_quizzes.cant edit attended general quiz')
            ], 422);
        }
        $data = $this->parser->deserialize($request->getContent())->getData();

        $usecase = $this->addQuestionBankToGeneralQuiz->addQuestions($homework, $data);
        $this->generalQuizRepository->updateGeneralQuizMark($homework);
        $include = '';
        if ($usecase['status'] == 200) {
            $meta = [
                'message' => $usecase['message'],
            ];
            return response()->json(['meta' => $meta], $usecase['status']);
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function questionBankList(Request $request, GeneralQuiz $homework)
    {
        $user = Auth::guard('api')->user();

        $questions = $this->questionBankRepository->getAvailableBankQuestion($homework, $request->get("public_status"));

        return $this->transformDataModInclude(
            $questions,
            'questionData',
            new ListBankQuestionsTransformer($homework),
            ResourceTypesEnums::GENERAL_QUIZ_QUESTION_BANK
        );
    }


    public function delete(GeneralQuiz $homework, GeneralQuizQuestionBank $question)
    {
        if (count($homework->studentsAnswered)) {
            return formatErrorValidation([
                'status' => 422,
                'title' => 'cant edit attended homework',
                'detail' => trans('general_quizzes.cant edit attended general quiz')
            ], 422);
        }

        if (!$question->generalQuiz->count()) {
            return abort(404);
        }

        $question->generalQuiz()->detach($homework->id);
        $this->generalQuizRepository->updateGeneralQuizMark($homework);

        return response()->json([
            'meta' => [
                'message' => trans('app.Deleted Successfully')
            ]
        ]);
    }

}
