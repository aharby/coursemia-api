<?php


namespace App\OurEdu\Quizzes\Student\Controllers\Api;


use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Requests\BaseApiRequest;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use App\OurEdu\Quizzes\Student\Middleware\QuizAuthenticationMiddleware;
use App\OurEdu\Quizzes\Student\Transformers\Homework\ClassroomHomeworkListTransformer;
use App\OurEdu\Quizzes\Student\Transformers\Homework\HomeworkQuestionTransformer;
use App\OurEdu\Quizzes\Student\Transformers\Homework\HomeworkTransformer;
use App\OurEdu\Quizzes\Transformers\HomeWork\HomeworksListTransformer;
use App\OurEdu\Quizzes\UseCases\AnswerQuizQuestionUseCase\AnswerQuizQuestionUseCaseInterface;
use App\OurEdu\Quizzes\UseCases\FinishQuizUseCase\FinishQuizUseCaseInterface;
use App\OurEdu\Quizzes\UseCases\NextBackUseCase\NextBackUseCaseInterface;
use App\OurEdu\Quizzes\UseCases\StartQuizUseCase\StartQuizUseCaseInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class HomeWorkApiController extends BaseApiController
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
            ->only(['startHomework','listHomework', 'getNextOrBackQuestion', 'finishHomework', 'postAnswer']);
//        $this->middleware(QuizAuthenticationMiddleware::class)
//            ->only(['getNextOrBackQuestion', 'finishHomework', 'postAnswer']);
    }

    public function listHomework()
    {
        try {
            $student = auth()->user()->student;
            $allHomework = $this->quizRepository->getClassroomHomework($student);
            return $this->transformDataModInclude(
                $allHomework, '',
                new ClassroomHomeworkListTransformer(), ResourceTypesEnums::HOMEWORK
            );
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function getHomework($homeworkId)
    {
        try {
            $quiz = $this->quizRepository->getRunningQuizDetails($homeworkId, QuizTypesEnum::HOMEWORK);

            $meta = [];
            if (!$quiz) {
                $meta = [
                    'message' => trans("Homework Not Valid Now")
                ];
            }

            return $this->transformDataModInclude(
                $quiz,
                '',
                new HomeWorkTransformer(),
                ResourceTypesEnums::HOMEWORK,
                $meta
            );
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    // start quiz and get first question
    public function startHomework($homeworkId)
    {
        $useCase = $this->startQuizUseCase->startQuiz($homeworkId);
        try {
            if ($useCase['status'] == 200) {
                $questions = $useCase['questions'];
                $params['next'] = $questions->nextPageUrl();
                $params['previous'] = $questions->previousPageUrl();

                return $this->transformDataModInclude(
                    $questions,
                    '',
                    new HomeworkQuestionTransformer($params),
                    ResourceTypesEnums::HOMEWORK_QUESTION,
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

    public function getNextOrBackQuestion(int $homeworkId)
    {
        $page = request()->input('page') ?? 1;
        try {
            $nextOrBackQuestion = $this->nextBackUseCase->nextOrBackQuestion($homeworkId, $page);

            if ($nextOrBackQuestion['status'] == 200) {
                $questions = $nextOrBackQuestion['questions'];

                $params = [
                    'next' => $questions->nextPageUrl(),
                    'previous' => $questions->previousPageUrl()
                ];

                $params['with_answers'] = request()->get('with_answers', false);
                if (isset($nextOrBackQuestion['last_question'])) {
                    $params['last_question'] = $nextOrBackQuestion['last_question'];
                }

                return $this->transformDataModInclude(
                    $questions,
                    '',
                    new HomeworkQuestionTransformer($params),
                    ResourceTypesEnums::HOMEWORK_QUESTION
                );
            } else {
                return formatErrorValidation($nextOrBackQuestion);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
        }
    }

    public function finishHomework(int $homeworkId)
    {
        try {
            $useCase = $this->finishQuizUseCase->finishQuiz($homeworkId);
            if ($useCase['status'] == 200) {
                $params['with_answers'] = request()->get('with_answers', false);
                $params['result'] = $useCase['result'];
                $meta = ['message' => trans('quiz.HomeWork is over')];

                return $this->transformDataModInclude($useCase['quiz'],
                    '',
                    new HomeworkTransformer($params),
                    ResourceTypesEnums::HOMEWORK,
                    $meta
                );
            } else {
                return formatErrorValidation($useCase);
            }
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function postAnswer(BaseApiRequest $request, $homeworkId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        try {
            DB::beginTransaction();
            $useCase = $this->answerQuizQuestionUseCase->postAnswer($homeworkId, $data);
            DB::commit();
            if ($useCase['status'] != 200) {
                return formatErrorValidation($useCase);
            }
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


}
