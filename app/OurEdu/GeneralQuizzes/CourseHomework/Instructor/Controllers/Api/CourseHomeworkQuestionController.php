<?php


namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Controllers\Api;

use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Middlewares\checkInstructorBelongToCourseMiddleware;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Middlewares\checkInstructorHasHomeworkMiddleware;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Requests\HomeworkQuestionRequest;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Requests\ReviewEssayQuestionRequest;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Transformers\QuestionBankTransformer;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\QuestionBankRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionBankTransformer as ListBankQuestionsTransformer;
use App\OurEdu\GeneralQuizzes\UseCases\GeneralUseCase\QuestionsUseCases\GeneralQuizQuestionUseCaseInterface;

class CourseHomeworkQuestionController extends BaseApiController
{
    public function __construct(
        private GeneralQuizQuestionUseCaseInterface $generalQuizQuestionUseCase,
        private QuestionBankRepositoryInterface $questionBankRepository,
        private GeneralQuizRepositoryInterface $generalQuizRepository,
        private ParserInterface $parser
    ) {
        $this->middleware(checkInstructorHasHomeworkMiddleware::class);
        $this->middleware('type:instructor');
    }

    public function list(GeneralQuiz $courseHomework)
    {
        $bankQuestions = $this->generalQuizRepository->getGeneralQuizQuestions($courseHomework);

        return $this->transformDataModInclude(
            $bankQuestions,
            'actions',
            new QuestionBankTransformer($courseHomework),
            ResourceTypesEnums::HOMEWORK_QUESTION
        );
    }

    public function view(GeneralQuiz $courseHomework, GeneralQuizQuestionBank $questionBank)
    {
        return $this->transformDataModIncludeItem(
            $questionBank,
            "actions",
            new QuestionBankTransformer($courseHomework),
            ResourceTypesEnums::HOMEWORK_QUESTION
        );
    }

    public function store(HomeworkQuestionRequest $request, GeneralQuiz $courseHomework)
    {
        if (count($courseHomework->studentsAnswered)) {
            return formatErrorValidation(
                [
                    'status' => 422,
                    'title' => 'cant edit attended homework',
                    'detail' => trans('general_quizzes.cant edit attended general quiz')
                ],
                422
            );
        }
        $data = $this->parser->deserialize($request->getContent())->getData();
        $data->public_status = false;
        try {
            DB::beginTransaction();
            $questionData = $this->generalQuizQuestionUseCase->addQuestion($courseHomework, $data);
            if (isset($questionData['errors'])) {
                return formatErrorValidation($questionData['errors']);
            }
            $this->generalQuizRepository->updateGeneralQuizMark($courseHomework);
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
            new QuestionBankTransformer($courseHomework),
            ResourceTypesEnums::HOMEWORK_QUESTION
        );
    }

    public function reviewEssay(
        ReviewEssayQuestionRequest $request,
        GeneralQuiz $courseHomework,
        GeneralQuizStudentAnswer $answer
    ) {
        $data = $this->parser->deserialize($request->getContent())->getData();

        $usecase = $this->generalQuizQuestionUseCase->reviewEssay($courseHomework, $answer, $data);
        if ($usecase['status'] == 200) {
            $meta = [
                'message' => $usecase['message'],
            ];
            return response()->json(['meta' => $meta], $usecase['status']);
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function delete(GeneralQuiz $courseHomework, GeneralQuizQuestionBank $question)
    {

        if (count($courseHomework->studentsAnswered)) {
            return formatErrorValidation(
                [
                    'status' => 422,
                    'title' => 'cant edit attended homework',
                    'detail' => trans('general_quizzes.cant edit attended general quiz')
                ],
                422
            );
        }

        if (!$question->generalQuiz->count()) {
            return abort(404);
        }

        $question->generalQuiz()->detach($courseHomework->id);
        $this->generalQuizRepository->updateGeneralQuizMark($courseHomework);

        return response()->json(
            [
                'meta' => [
                    'message' => trans('app.Deleted Successfully')
                ]
            ]
        );
    }
}
