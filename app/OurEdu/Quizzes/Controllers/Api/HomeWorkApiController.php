<?php


namespace App\OurEdu\Quizzes\Controllers\Api;


use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepositoryInterface;
use App\OurEdu\Quizzes\Requests\CreateQuizRequest;
use App\OurEdu\Quizzes\Requests\HomeWork\CreateHomeWorkRequest;
use App\OurEdu\Quizzes\Requests\HomeWork\UpdateHomeWorkRequest;
use App\OurEdu\Quizzes\Requests\UpdateQuizQuestionsRequest;
use App\OurEdu\Quizzes\Requests\UpdateQuizRequest;
use App\OurEdu\Quizzes\Transformers\HomeWork\HomeWorkQuestionTransformer;
use App\OurEdu\Quizzes\Transformers\HomeWork\HomeworksListTransformer;
use App\OurEdu\Quizzes\Transformers\HomeWork\HomeWorkStudentListTransformer;
use App\OurEdu\Quizzes\Transformers\HomeWork\HomeWorkStudentTransformer;
use App\OurEdu\Quizzes\Transformers\HomeWork\HomeWorkTransformer;
use App\OurEdu\Quizzes\Transformers\QuizQuestionTransformer;
use App\OurEdu\Quizzes\Transformers\QuizStudentsListTransformer;
use App\OurEdu\Quizzes\Transformers\QuizStudentTransformer;
use App\OurEdu\Quizzes\Transformers\QuizTransformer;
use App\OurEdu\Quizzes\Transformers\QuizzesListTransformer;
use App\OurEdu\Quizzes\UseCases\HomeWorkUseCase\HomeWorkUseCaseInterface;
use App\OurEdu\Quizzes\UseCases\QuizQuestionUseCase\QuizQuestionUseCaseInterface;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use GPBMetadata\Google\Api\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class HomeWorkApiController extends BaseApiController
{
    private $quizRepository;
    private $homeWorkUseCase;
    private $parserInterface;
    private $quizQuestionUseCase;


    public function __construct(
        QuizRepositoryInterface $quizRepository,
        HomeWorkUseCaseInterface $homeWorkUseCase,
        QuizQuestionUseCaseInterface $quizQuestionUseCase,
        ParserInterface $parserInterface
    ) {
        $this->quizRepository = $quizRepository;
        $this->homeWorkUseCase = $homeWorkUseCase;
        $this->quizQuestionUseCase = $quizQuestionUseCase;
        $this->parserInterface = $parserInterface;

        $this->middleware('type:' .  UserEnums::EDUCATIONAL_SUPERVISOR  . '|' . UserEnums::SCHOOL_INSTRUCTOR)->only(['createHomeWork','updateHomeworkQuestions','delete']);
    }

    public function listAllHomeworks()
    {
        $this->setFilters();



        try {
            $user = auth()->user();
            $quizzes = $this->quizRepository->getAllHomeWorksByUser($user , $this->filters);

            $meta = [
                'filters' => formatFiltersForApi($this->filters)
            ];
            return $this->transformDataModInclude(
                $quizzes,
                '',
                new HomeworksListTransformer(),
                ResourceTypesEnums::HOMEWORK , $meta
            );
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function getHomework($homeworkId)
    {
        try {
            $useCase = $this->homeWorkUseCase->getHomeWork($homeworkId);
            return $this->transformDataModInclude(
                $useCase['quiz'],
                '',
                new HomeWorkTransformer(),
                ResourceTypesEnums::HOMEWORK
            );
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function createHomeWork(CreateHomeWorkRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $useCase = $this->homeWorkUseCase->createHomeWork($data);
        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['quiz'],
                '',
                new HomeWorkTransformer(),
                ResourceTypesEnums::HOMEWORK,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function editHomeWork(UpdateHomeWorkRequest $request, $homeworkId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $quiz = $this->quizRepository->findOrFail($homeworkId);
        $useCase = $this->homeWorkUseCase->editHomeWork($quiz, $data->toArray());

        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['quiz'],
                '',
                new HomeWorkTransformer(),
                ResourceTypesEnums::HOMEWORK,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function publishHomeWork($homeworkId)
    {
        return $this->homeWorkUseCase->publishHomework($homeworkId);
    }

    public function delete($homeworkId)
    {
        try {
            $quiz = $this->quizRepository->findOrFail($homeworkId);
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

    public function getQuizQuestions($homeworkId)
    {
        try {
            $quiz = $this->quizRepository->findOrFail($homeworkId);
            return $this->transformDataModInclude($quiz->questions, '',
                new QuizQuestionTransformer(), ResourceTypesEnums::QUIZ_QUESTION);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function updateHomeworkQuestions(UpdateQuizQuestionsRequest $request, $homeworkId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        try {
            $quiz = $this->quizRepository->findOrFail($homeworkId);
            $useCase = $this->quizQuestionUseCase->createOrUpdateQuizQuestions($quiz, $data);

            if ($useCase['status'] == 200) {
                return $this->transformDataModInclude(
                    $quiz->questions,
                    '',
                    new HomeWorkQuestionTransformer(),
                    ResourceTypesEnums::HOMEWORK_QUESTION
                );
            } else {
                return formatErrorValidation($useCase);
            }
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function listHomeworkStudents($homeworkId)
    {
        $students = $this->quizRepository->listQuizStudents($homeworkId);
        return $this->transformDataModInclude(
            $students,
            '',
            new HomeWorkStudentListTransformer(),
            ResourceTypesEnums::HOMEWORK_Student
        );
    }

    public function getStudentHomework($homeworkId, $studentId)
    {
        $student = $this->quizRepository->getStudentQuiz($homeworkId, $studentId);
        return $this->transformDataModInclude(
            $student,
            '',
            new HomeWorkStudentTransformer(),
            ResourceTypesEnums::HOMEWORK_Student
        );
    }

    public function getSessionHomework($classroomSessionId)
    {
        try {
            $quizzes = $this->quizRepository->getSessionHomework($classroomSessionId);
            return $this->transformDataModInclude(
                $quizzes,
                '',
                new HomeworksListTransformer(),
                ResourceTypesEnums::HOMEWORK
            );
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

    protected function setFilters(){
        $classroomInstractors = auth()->user()->schoolInstructorBranch->classrooms->pluck('name' , 'id')->toArray();


        $this->filters[] = [
            'name' => 'classroom_id',
            'type' => 'select',
            'data' => $classroomInstractors,
            'trans' => false,
            'value' => request()->get('classroom_id'),
        ];
    }

}
