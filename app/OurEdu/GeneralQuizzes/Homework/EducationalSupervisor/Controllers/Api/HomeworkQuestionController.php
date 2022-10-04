<?php


namespace App\OurEdu\GeneralQuizzes\Homework\EducationalSupervisor\Controllers\Api;


use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsPublicStatusesEnums;
use App\OurEdu\GeneralQuizzes\Homework\EducationalSupervisor\Requests\HomeWorkQuestionRequest;
use App\OurEdu\GeneralQuizzes\Homework\EducationalSupervisor\Transformers\QuestionBankTransformer;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Requests\AddQuestionsFromQuestionBankRequest;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\QuestionBankRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionBankTransformer as ListBankQuestionsTransformer;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\QuestionTransformer;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\AddQuestionBankToGeneralQuizInterface;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralQuizQuestionUseCaseInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\GeneralQuizzes\Middlewares\EducationalSupervisorMiddleware;

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
     * @var GeneralQuizRepositoryInterface
     */
    private $generalQuizRepository;
    /**
     * @var AddQuestionBankToGeneralQuizInterface
     */
    private AddQuestionBankToGeneralQuizInterface $addQuestionBankToGeneralQuiz;
    /**
     * @var QuestionBankRepositoryInterface
     */
    private QuestionBankRepositoryInterface $questionBankRepository;

    /**
     * HomeWorkQuestionController constructor.
     * @param GeneralQuizQuestionUseCaseInterface $generalQuizQuestionUseCase
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     * @param AddQuestionBankToGeneralQuizInterface $addQuestionBankToGeneralQuiz
     * @param QuestionBankRepositoryInterface $questionBankRepository
     * @param ParserInterface $parser
     */
    public function __construct(
        GeneralQuizQuestionUseCaseInterface $generalQuizQuestionUseCase,
        GeneralQuizRepositoryInterface $generalQuizRepository,
        AddQuestionBankToGeneralQuizInterface $addQuestionBankToGeneralQuiz,
        QuestionBankRepositoryInterface $questionBankRepository,
        ParserInterface $parser
    )
    {
        $this->generalQuizQuestionUseCase = $generalQuizQuestionUseCase;
        $this->parser = $parser;
        $this->generalQuizRepository = $generalQuizRepository;
        $this->addQuestionBankToGeneralQuiz = $addQuestionBankToGeneralQuiz;
        $this->questionBankRepository = $questionBankRepository;
        $this->middleware('type:educational_supervisor');
        $this->middleware(EducationalSupervisorMiddleware::class)->only(['store','delete']);
    }

    public function list(GeneralQuiz $homework)
    {
        $bankQuestions = $this->generalQuizRepository->getGeneralQuizQuestions($homework);
        return $this->transformDataModInclude($bankQuestions, 'actions', new QuestionBankTransformer($homework), ResourceTypesEnums::HOMEWORK_QUESTION);
    }

    public function view(GeneralQuiz $homework, GeneralQuizQuestionBank $questionBank)
    {
        $params['viewQuestion'] = true;
        return $this->transformDataModIncludeItem($questionBank, "actions", new QuestionBankTransformer($homework,$params), ResourceTypesEnums::HOMEWORK_QUESTION);
    }

    public function store(HomeWorkQuestionRequest $request, GeneralQuiz $homework)
    {
        // add validations for edu
        if ($homework->published_at) {
            return formatErrorValidation([
                'status' => 422,
                'title' =>  $request->homework->quiz_type.' is published',
                'detail' => trans('general_quizzes.cannot add any questions quiz already published',[
                    'quiz_type'=> trans('general_quizzes.'.$request->homework->quiz_type)
                ])
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
        return $this->transformDataModInclude($questionBank, $include, new QuestionBankTransformer($homework), ResourceTypesEnums::HOMEWORK_QUESTION);
    }

    public function delete(GeneralQuiz $homework,  GeneralQuizQuestionBank $question)
    {
        if ($homework->published_at) {
            $error['status'] = 422;
            $error['detail'] = trans("general_quizzes.cannot delete any questions quiz already published",
            ['quiz_type'=> trans('general_quizzes.'.$homework->quiz_type)]);
            $error['title'] = 'Homework published';

           return formatErrorValidation($error);
        }

        if(!$question->generalQuiz->count()){

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

    public function questionBankList(Request $request,GeneralQuiz $homework)
    {
        $user = Auth::guard('api')->user();
        $questions =  $this->questionBankRepository->getAvailableBankQuestion($homework, $request->get("public_status"));

        return $this->transformDataModInclude(
            $questions,
            'questionData',
            new ListBankQuestionsTransformer($homework),
            ResourceTypesEnums::GENERAL_QUIZ_QUESTION_BANK
        );

    }


    public function addQuestionBankToGeneralQuiz(AddQuestionsFromQuestionBankRequest $request, GeneralQuiz $homework)
    {

        if ($homework->published_at) {
            return formatErrorValidation([
                'status' => 422,
                'title' =>  $request->homework->quiz_type.' is published',
                'detail' => trans('general_quizzes.cannot add any questions quiz already published',[
                    'quiz_type'=> $request->homework->quiz_type
                ])
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

}
