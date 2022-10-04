<?php


namespace App\OurEdu\Quizzes\Controllers\Api;


use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use App\OurEdu\Quizzes\Requests\PeriodicTest\CreatePeriodicTestRequest;
use App\OurEdu\Quizzes\Requests\PeriodicTest\UpdatePeriodicTestRequest;
use App\OurEdu\Quizzes\Requests\UpdateQuizQuestionsRequest;
use App\OurEdu\Quizzes\Transformers\PeriodicTest\PeriodicTestListTransformer;
use App\OurEdu\Quizzes\Transformers\PeriodicTest\PeriodicTestQuestionTransformer;
use App\OurEdu\Quizzes\Transformers\PeriodicTest\PeriodicTestStudentListTransformer;
use App\OurEdu\Quizzes\Transformers\PeriodicTest\PeriodicTestStudentTransformer;
use App\OurEdu\Quizzes\Transformers\PeriodicTest\PeriodicTestTransformer;
use App\OurEdu\Quizzes\UseCases\PeriodicTestUseCase\PeriodicTestUseCase;
use App\OurEdu\Quizzes\UseCases\QuizQuestionUseCase\QuizQuestionUseCaseInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use Throwable;
use App\OurEdu\Users\UserEnums;

class PeriodicTestApiController extends BaseApiController
{
    private $quizRepository;
    private $PeriodicTestUseCase;
    private $parserInterface;
    private $quizQuestionUseCase;

    public function __construct(
        QuizRepositoryInterface $quizRepository,
        PeriodicTestUseCase $PeriodicTestUseCase,
        QuizQuestionUseCaseInterface $quizQuestionUseCase,
        ParserInterface $parserInterface
    )
    {
        $this->quizRepository = $quizRepository;
        $this->PeriodicTestUseCase = $PeriodicTestUseCase;
        $this->quizQuestionUseCase = $quizQuestionUseCase;
        $this->parserInterface = $parserInterface;

        $this->middleware('type:' .  UserEnums::EDUCATIONAL_SUPERVISOR  . '|' . UserEnums::SCHOOL_INSTRUCTOR)->only(['createPeriodicTest','editPeriodicTest','delete']);
    }

    public function listAllPeriodicTest()
    {
        $this->setFilters();

        try {
            $user = auth()->user();
            $quizzes = $this->quizRepository->getAllPeriodicTestsByUser($user, $this->filters);

            $meta = [
                'filters' => formatFiltersForApi($this->filters)
            ];

            return $this->transformDataModInclude(
                $quizzes,
                '',
                new PeriodicTestListTransformer(),
                ResourceTypesEnums::Periodic_Test,
                $meta
            );
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    protected function setFilters()
    {
        $GradeClassInstractors = auth()->user()->schoolInstructorSubjects->pluck('grade_class_id')->toArray();


        $this->filters[] = [
            'name' => 'grade_class_id',
            'type' => 'select',
            'data' => $GradeClassInstractors,
            'trans' => false,
            'value' => request()->get('grade_class_id'),
        ];
    }

    public function getPeriodicTest($periodicTestId)
    {
        try {
            $useCase = $this->PeriodicTestUseCase->getPeriodicTest($periodicTestId);
            return $this->transformDataModInclude(
                $useCase['quiz'],
                '',
                new PeriodicTestTransformer(),
                ResourceTypesEnums::Periodic_Test
            );
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function delete($periodicTestId)
    {
        try {
            $quiz = $this->quizRepository->findOrFail($periodicTestId);
            if (now()->lte(Carbon::parse($quiz->start_at)) || !$quiz->published_at) {
                if ($this->quizRepository->setQuiz($quiz)->delete()) {
                    return response()->json([
                        'meta' => [
                            'message' => trans('api.Deleted Successfully')
                        ]
                    ]);
                }
            }
            throw new OurEduErrorException(trans('api.You cant delete Periodic Test after publishing'));
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function createPeriodicTest(CreatePeriodicTestRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $useCase = $this->PeriodicTestUseCase->createPeriodicTest($data);
        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['quiz'],
                '',
                new PeriodicTestTransformer(),
                ResourceTypesEnums::Periodic_Test,
                $useCase['meta']
            );

        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function editPeriodicTest(UpdatePeriodicTestRequest $request, $periodicTestId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $quiz = $this->quizRepository->findOrFail($periodicTestId);
        $useCase = $this->PeriodicTestUseCase->editPeriodicTest($quiz, $data->toArray());

        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['quiz'],
                '',
                new PeriodicTestTransformer(),
                ResourceTypesEnums::Periodic_Test,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function publishPeriodicTest($periodicTestId)
    {
        try {
            $quiz = $this->quizRepository->findOrFail($periodicTestId);
            if ((new \Carbon\Carbon($quiz->start_at))->subHour() < now()) {
                if (new \Carbon\Carbon($quiz->start_at) < now()) {
                    return formatErrorValidation([
                        'status' => 422,
                        'title' => 'The periodic test has passed',
                        'detail' => trans('api.The periodic test already started')
                    ], 422);
                }
                return formatErrorValidation([
                    'status' => 422,
                    'title' => 'The periodic test has passed',
                    'detail' => trans('api.Can not update periodic test which will start in less than one hour')
                ], 422);
            }
            if ($quiz->questions()->count() > 0) {
                if (!is_null($quiz->published_at)) {
                    return response()->json([
                        'meta' => [
                            'message' => trans('api.Periodic Test Already published')
                        ]
                    ]);
                }
                if ($this->quizRepository->setQuiz($quiz)->update(['published_at' => now()])) {
                    return response()->json([
                        'meta' => [
                            'message' => trans('api.Published Successfully')
                        ]
                    ]);
                }
            }
            return formatErrorValidation([
                'status' => 422,
                'title' => 'The homework time has passed',
                'detail' => trans('api.You should put questions first.')
            ], 422);
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function getPeriodicTestQuestions($periodicTestId)
    {
        try {
            $quiz = $this->quizRepository->findOrFail($periodicTestId);
            return $this->transformDataModInclude($quiz->questions, '',
                new PeriodicTestQuestionTransformer(), ResourceTypesEnums::Periodic_Test_QUESTION);
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function updatePeriodicTestQuestions(UpdateQuizQuestionsRequest $request, $periodicTestId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        try {
            $quiz = $this->quizRepository->findOrFail($periodicTestId);
            $useCase = $this->quizQuestionUseCase->createOrUpdateQuizQuestions($quiz, $data);

            if ($useCase['status'] == 200) {
                return $this->transformDataModInclude(
                    $quiz->questions,
                    '',
                    new PeriodicTestQuestionTransformer(),
                    ResourceTypesEnums::Periodic_Test_QUESTION
                );
            } else {
                return formatErrorValidation($useCase);
            }
        } catch (Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function listPeriodicTestStudents($periodicTestId)
    {
        $students = $this->quizRepository->listQuizStudents($periodicTestId);
        return $this->transformDataModInclude(
            $students,
            '',
            new PeriodicTestStudentListTransformer(),
            ResourceTypesEnums::Periodic_Test_Student
        );
    }

    public function getStudentPeriodicTest($periodicTestId, $studentId)
    {
        $student = $this->quizRepository->getStudentQuiz($periodicTestId, $studentId);
        return $this->transformDataModInclude(
            $student,
            '',
            new PeriodicTestStudentTransformer(),
            ResourceTypesEnums::Periodic_Test_Student
        );
    }

}
