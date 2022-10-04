<?php

namespace App\OurEdu\Quizzes\Controllers\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use App\OurEdu\Quizzes\Requests\CreateQuizRequest;
use App\OurEdu\Quizzes\Requests\UpdateQuizQuestionsRequest;
use App\OurEdu\Quizzes\Requests\UpdateQuizRequest;
use App\OurEdu\Quizzes\Transformers\QuizQuestionTransformer;
use App\OurEdu\Quizzes\Transformers\QuizStudentsListTransformer;
use App\OurEdu\Quizzes\Transformers\QuizStudentTransformer;
use App\OurEdu\Quizzes\Transformers\QuizTransformer;
use App\OurEdu\Quizzes\Transformers\QuizzesListTransformer;
use App\OurEdu\Quizzes\UseCases\QuizQuestionUseCase\QuizQuestionUseCaseInterface;
use App\OurEdu\Quizzes\UseCases\QuizUseCase\QuizUseCaseInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class QuizApiController extends BaseApiController
{
    private $quizRepository;
    private $quizUseCase;
    private $parserInterface;
    private $quizQuestionUseCase;

    public function __construct(
        QuizRepositoryInterface $quizRepository,
        QuizUseCaseInterface $quizUseCase,
        QuizQuestionUseCaseInterface $quizQuestionUseCase,
        ParserInterface $parserInterface
    ) {
        $this->quizRepository = $quizRepository;
        $this->quizUseCase = $quizUseCase;
        $this->quizQuestionUseCase = $quizQuestionUseCase;
        $this->parserInterface = $parserInterface;
    }

    public function listAllQuizzes()
    {
        $this->setFilters();

        try {
            $user = auth()->user();
            $quizzes = $this->quizRepository->getAllQuizzesByUser($user , $this->filters);

            $meta = [
                'filters' => formatFiltersForApi($this->filters)
            ];
            return $this->transformDataModInclude(
                $quizzes,
                '',
                new QuizzesListTransformer(),
                ResourceTypesEnums::QUIZ,
                $meta
            );
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function getQuiz($quizId)
    {
        try {
            $useCase = $this->quizUseCase->getQuiz($quizId);
            return $this->transformDataModInclude(
                $useCase['quiz'],
                '',
                new QuizTransformer(),
                ResourceTypesEnums::QUIZ
            );
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function delete($quizId)
    {
        try {
            $quiz = $this->quizRepository->findOrFail($quizId);
            if (now()->lte(Carbon::parse($quiz->start_at)) || ! $quiz->published_at) {
                if ($this->quizRepository->setQuiz($quiz)->delete()) {
                    return response()->json([
                        'meta' => [
                            'message' => trans('api.Deleted Successfully')
                        ]
                    ]);
                }
            }
            throw new OurEduErrorException(trans('api.You cant delete quiz after publishing'));
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function createQuiz(CreateQuizRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();


        $useCase = $this->quizUseCase->createQuiz($data);
        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['quiz'],
                '',
                new QuizTransformer(),
                ResourceTypesEnums::QUIZ,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function editQuiz(UpdateQuizRequest $request, $quizId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $quiz = $this->quizRepository->findOrFail($quizId);
        $useCase = $this->quizUseCase->editQuiz($quiz, $data->toArray());

        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['quiz'],
                '',
                new QuizTransformer(),
                ResourceTypesEnums::QUIZ,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function publishQuiz($quizId)
    {
        return $this->quizUseCase->publishQuiz($quizId);
    }

    public function getQuizQuestions($quizId)
    {
        try {
            $quiz = $this->quizRepository->findOrFail($quizId);
            return $this->transformDataModInclude($quiz->questions, '',
                new QuizQuestionTransformer(), ResourceTypesEnums::QUIZ_QUESTION);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function updateQuizQuestions(UpdateQuizQuestionsRequest $request, $quizId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        try {
            $quiz = $this->quizRepository->findOrFail($quizId);
            $useCase = $this->quizQuestionUseCase->createOrUpdateQuizQuestions($quiz, $data);

            if ($useCase['status'] == 200) {
                return $this->transformDataModInclude(
                    $quiz->questions,
                    '',
                    new QuizQuestionTransformer(),
                    ResourceTypesEnums::QUIZ_QUESTION
                );
            } else {
                return formatErrorValidation($useCase);
            }
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function listQuizStudents($quizId)
    {
        $students = $this->quizRepository->listQuizStudents($quizId);
        return $this->transformDataModInclude(
            $students,
            '',
            new QuizStudentsListTransformer(),
            ResourceTypesEnums::QUIZ_STUDENT
        );
    }

    public function getStudentQuiz($quizId, $studentId)
    {
        $student = $this->quizRepository->getStudentQuiz($quizId, $studentId);
        return $this->transformDataModInclude(
            $student,
            '',
            new QuizStudentTransformer(),
            ResourceTypesEnums::QUIZ_STUDENT
        );
    }

    public function getSessionQuizzes($classroomSessionId)
    {
        try {
            $quizzes = $this->quizRepository->getSessionQuizzes($classroomSessionId);
            return $this->transformDataModInclude(
                $quizzes,
                '',
                new QuizzesListTransformer(),
                ResourceTypesEnums::QUIZ
            );
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function listAllQuizzesTypes()
    {
        $this->setFilters();

        try {
            $user = auth()->user();
            $quizzes = $this->quizRepository->getAllQuizzesTypesByUser($user , $this->filters);

            $meta = [
                'filters' => formatFiltersForApi($this->filters)
            ];
            return $this->transformDataModInclude(
                $quizzes,
                '',
                new QuizzesListTransformer(),
                ResourceTypesEnums::QUIZ,
                $meta
            );
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    protected function setFilters(){
        $classroomInstractors = auth()->user()->schoolInstructorBranch->classrooms->pluck('name' , 'id')->toArray();

        // if(request()->is('*/all-quizzes*')){
            $this->filters[] = [
                'name' => 'quiz_type',
                'type' => 'select',
                'data' => QuizTypesEnum::getAllQuizTypes(),
                'trans' => false,
                'value' => request()->get('quiz_type'),
            ];
        // }
        // else{
            $this->filters[] = [
                'name' => 'classroom_id',
                'type' => 'select',
                'data' => $classroomInstractors,
                'trans' => false,
                'value' => request()->get('classroom_id'),
            ];
        // }
    }
}
