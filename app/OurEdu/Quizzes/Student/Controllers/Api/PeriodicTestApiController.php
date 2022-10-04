<?php


namespace App\OurEdu\Quizzes\Student\Controllers\Api;


use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use App\OurEdu\Quizzes\Student\Middleware\QuizAuthenticationMiddleware;
use App\OurEdu\Quizzes\Student\Transformers\Homework\ClassroomHomeworkListTransformer;
use App\OurEdu\Quizzes\Student\Transformers\PeriodicTest\StudentPeriodicTestTransformer;
use App\OurEdu\Quizzes\Student\Transformers\PeriodicTest\PeriodicTestListTransformer;
use App\OurEdu\Quizzes\Student\Transformers\PeriodicTest\PeriodicTestQuestionTransformer;
use App\OurEdu\Quizzes\Student\Transformers\PeriodicTest\PeriodicTestTransformer;
use App\OurEdu\Quizzes\Student\Transformers\QuizQuestionTransformer;
use App\OurEdu\Quizzes\Student\Transformers\QuizTransformer;
use App\OurEdu\Quizzes\Student\Transformers\QuizzesListTransformer;
use App\OurEdu\Quizzes\UseCases\AnswerQuizQuestionUseCase\AnswerQuizQuestionUseCaseInterface;
use App\OurEdu\Quizzes\UseCases\FinishQuizUseCase\FinishQuizUseCaseInterface;
use App\OurEdu\Quizzes\UseCases\NextBackUseCase\NextBackUseCaseInterface;
use App\OurEdu\Quizzes\UseCases\StartQuizUseCase\StartQuizUseCaseInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class PeriodicTestApiController  extends BaseApiController
{

    private $quizRepository;
    private $startQuizUseCase;
    private $finishQuizUseCase;
    private $nextBackUseCase;
    private $parserInterface;
    private $answerQuizQuestionUseCase;

    public function __construct(
        QuizRepositoryInterface $quizRepository,
        StartQuizUseCaseInterface $startQuizUseCase,
        FinishQuizUseCaseInterface $finishQuizUseCase,
        NextBackUseCaseInterface $nextBackUseCase,
        AnswerQuizQuestionUseCaseInterface $answerQuizQuestionUseCase,
        ParserInterface $parserInterface
    ) {
        $this->quizRepository = $quizRepository;
        $this->startQuizUseCase = $startQuizUseCase;
        $this->finishQuizUseCase = $finishQuizUseCase;
        $this->nextBackUseCase = $nextBackUseCase;
        $this->answerQuizQuestionUseCase = $answerQuizQuestionUseCase;
        $this->parserInterface = $parserInterface;
        $this->middleware('type:student')
            ->only(['startHomework', 'getPeriodicTest', 'listPeriodicTest',
                'getNextOrBackQuestion', 'finishHomework', 'postAnswer']);
        $this->middleware(QuizAuthenticationMiddleware::class)
            ->only(['getNextOrBackQuestion', 'finishQuiz', 'postAnswer']);
    }

    public function getPeriodicTest($periodicTestId)
    {
        $quiz = $this->quizRepository->findOrFail($periodicTestId);
        $params = [
            'start_quiz' => true
        ];
        return $this->transformDataModInclude(
            $quiz,
            '',
            new PeriodicTestTransformer($params),
            ResourceTypesEnums::Periodic_Test
        );
    }

    // start quiz and get first question
    public function startPeriodicTest($periodicTestId)
    {
        $useCase = $this->startQuizUseCase->startQuiz($periodicTestId);
        try {
            if ($useCase['status'] == 200) {
                $questions = $useCase['questions'];
                $params['next'] = $questions->nextPageUrl();
                $params['previous'] = $questions->previousPageUrl();

                return $this->transformDataModInclude(
                    $questions,
                    '',
                    new PeriodicTestQuestionTransformer($params),
                    ResourceTypesEnums::Periodic_Test,
                    $useCase['message']
                );
            } else {
                return formatErrorValidation($useCase);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
        }
    }

    public function getNextOrBackQuestion(int $periodicTestId, Request $request)
    {
        $page = request()->input('page') ?? 1;
        try {
            $nextOrBackQuestion = $this->nextBackUseCase->nextOrBackQuestion($periodicTestId, $page);

            if ($nextOrBackQuestion['status'] == 200) {
                $questions = $nextOrBackQuestion['questions'];
//
                $params['next'] = $questions->nextPageUrl();
                if (isset($nextOrBackQuestion['last_question'])) {
                    $params['last_question'] = $nextOrBackQuestion['last_question'];
                }
                $params['previous'] = $questions->previousPageUrl();

                return $this->transformDataModInclude(
                    $questions,
                    '',
                    new PeriodicTestQuestionTransformer($params),
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

    public function finishPeriodicTest(int $periodicTestId)
    {
        try {
            $useCase = $this->finishQuizUseCase->finishQuiz($periodicTestId);
            if ($useCase['status'] == 200) {
                $params['result'] = $useCase['result'];
                $meta = ['message' => trans('quiz.Periodic Test is over')];

                return $this->transformDataModInclude($useCase['quiz'],
                    '',
                    new PeriodicTestTransformer($params),
                    ResourceTypesEnums::Periodic_Test, $meta);
            } else {
                return formatErrorValidation($useCase);
            }
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function postAnswer(BaseApiRequest $request, $periodicTestId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        try {
            DB::beginTransaction();

            $this->answerQuizQuestionUseCase->postAnswer($periodicTestId, $data);
            DB::commit();

            return response()->json([
                'meta' => [
                    'message' => trans('exam.Answered successfully')
                ]
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }


    public function listPeriodicTest()
    {
        try {
            $student = auth()->user()->student;
            $periodicTests = $this->quizRepository->getStudentPeriodicTest($student);
            return $this->transformDataModInclude(
                $periodicTests, '',
                new StudentPeriodicTestTransformer(),
                ResourceTypesEnums::Periodic_Test
            );
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

}
