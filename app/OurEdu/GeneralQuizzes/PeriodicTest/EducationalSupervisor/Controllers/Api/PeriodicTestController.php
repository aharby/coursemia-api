<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\EducationalSupervisor\Controllers\Api;


use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Middlewares\EducationalSupervisorMiddleware;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\PeriodicTest\EducationalSupervisor\Requests\UpdatePeriodicTestRequest;
use App\OurEdu\GeneralQuizzes\PeriodicTest\EducationalSupervisor\Transformers\PeriodicTestTransformer;
use App\OurEdu\GeneralQuizzes\PeriodicTest\EducationalSupervisor\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Transformers\QuestionViewAsStudentTransformer;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\UseCases\UpdatePeriodicTestUseCase\UpdatePeriodicTestUseCase;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\UseCases\ViewAsStudentUseCase\ViewAsStudentUseCaseInterface;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class PeriodicTestController extends BaseApiController
{
    /**
     * @var ParserInterface
     */
    private $parserInterface;
    /**
     * @var GeneralQuizRepositoryInterface
     */
    private $generalQuizRepository;
    /**
     * @var array
     */
    private $filters;
    /**
     * @var UpdatePeriodicTestUseCase
     */
    private $updatePeriodicTestUseCase;
    /**
     * @var ViewAsStudentUseCaseInterface
     */
    private ViewAsStudentUseCaseInterface $viewAsStudentUseCase;

    /**
     * PeriodicTestController constructor.
     * @param ParserInterface $parserInterface
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     * @param UpdatePeriodicTestUseCase $updatePeriodicTestUseCase
     * @param ViewAsStudentUseCaseInterface $viewAsStudentUseCase
     */
    public function __construct(
        ParserInterface $parserInterface,
        GeneralQuizRepositoryInterface $generalQuizRepository,
        UpdatePeriodicTestUseCase $updatePeriodicTestUseCase,
        ViewAsStudentUseCaseInterface $viewAsStudentUseCase
    ) {
        $this->parserInterface = $parserInterface;
        $this->generalQuizRepository = $generalQuizRepository;
        $this->updatePeriodicTestUseCase = $updatePeriodicTestUseCase;
        $this->filters = [];


        $this->middleware('type:educational_supervisor');
        $this->middleware(EducationalSupervisorMiddleware::class)->only(['edit', 'deactivate']);
        $this->viewAsStudentUseCase = $viewAsStudentUseCase;
    }

    public function index()
    {
        $this->setFilters();

        $date = request()->input('date');
        $instructor = request()->input('instructor');

        $periodicTests = $this->generalQuizRepository->listEducationalSupervisorGeneralQuizzes(
            Auth::user(),
            $this->filters,
            request()->get('classroom_id'),
            GeneralQuizTypeEnum::PERIODIC_TEST,
            $instructor,
            $date
        );

        return $this->transformDataModInclude(
            $periodicTests,
            "gradeClass,subject",
            new PeriodicTestTransformer(),
            ResourceTypesEnums::Periodic_Test
        );
    }

    public function show(GeneralQuiz $periodicTest)
    {
        return $this->transformDataModInclude(
            $periodicTest,
            'classrooms,classroomStudents',
            new PeriodicTestTransformer(),
            ResourceTypesEnums::Periodic_Test
        );
    }

    public function edit(UpdatePeriodicTestRequest $request, GeneralQuiz $periodicTest)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $useCase = $this->updatePeriodicTestUseCase->updatePeriodicTest($periodicTest->id, $data);
        if (isset($questionData['errors'])) {
            return formatErrorValidation($questionData['errors']);
        }
        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['periodicTest'],
                'classrooms,classroomStudents',
                new PeriodicTestTransformer(),
                ResourceTypesEnums::Periodic_Test,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function deactivate(GeneralQuiz $periodicTest)
    {
        $useCase = $this->updatePeriodicTestUseCase->deactivatePeriodicTest($periodicTest);

        if ($useCase['status'] == 200) {
            return response()->json([
                'meta' => [
                    'message' => trans('general_quizzes.Deactivated successfully')
                ]
            ]);
        } else {
            return formatErrorValidation($useCase);
        }
    }


    public function getPeriodicTestSection(GeneralQuiz $periodicTest)
    {
        return $this->transformDataMod(
            $periodicTest->sections,
            new SubjectFormatSubjectTransformer(),
            ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
        );
    }


    protected function setFilters()
    {
        $eduSupervisor = Auth::user();

        $branches = $eduSupervisor->branches->pluck("name", 'id')->toArray() ?? [];
        $this->filters[] = [
            'name' => 'branch_id',
            'type' => 'id',
            'data' => $branches,
            'trans' => false,
            'value' => request()->get('branch_id'),
        ];

        $subjects = $eduSupervisor->educationalSupervisorSubjects->pluck('name', 'id')->toArray();
        $this->filters[] = [
            'name' => 'subject_id',
            'type' => 'id',
            'data' => $subjects,
            'trans' => false,
            'value' => request()->get('subject_id'),
        ];

        $gradeClasses = $eduSupervisor->educationalSupervisorSubjects->pluck(
            'gradeClass.title',
            'gradeClass.id'
        )->toArray();
        $this->filters[] = [
            'name' => 'grade_class_id',
            'type' => 'id',
            'data' => $gradeClasses,
            'trans' => false,
            'value' => request()->get('grade_class_id'),
        ];

        $this->filters[] = [
            'name' => 'created_by',
            'type' => 'id',
            'data' => [],
            'trans' => false,
            'value' => request()->get('instructor_id'),
        ];
    }


    public function deactivateHomework(GeneralQuiz $periodicTest)
    {
        $useCase = $this->updatePeriodicTestUseCase->deactivateHomework($periodicTest);

        if ($useCase['status'] == 200) {
            return response()->json([
                'meta' => [
                    'message' => trans('general_quizzes.Deactivated successfully')
                ]
            ]);
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function preview(GeneralQuiz $periodicTest)
    {
        $page = request('page') ?? 1;
        $usecase = $this->viewAsStudentUseCase->nextOrBackQuestion($periodicTest->id, $page);
        if ($usecase['status'] == 200) {
            $params["student"] = Auth::user();
            $bankQuestions = $usecase['bankQuestions'];
            $generalQuiz = $usecase['generalQuiz'];

            if (isset($usecase['last_question'])) {
                $params['finish_general_quiz'] = true;
            }
            return $this->transformDataModInclude(
                $bankQuestions,
                'questions',
                new QuestionViewAsStudentTransformer($generalQuiz, $params),
                ResourceTypesEnums::Periodic_Test_QUESTION
            );
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function delete(GeneralQuiz $periodicTest)
    {
        if (!in_array($periodicTest->branch_id, \auth()->user()->branches->pluck('id')->toArray()) ||
            !in_array($periodicTest->subject_id, \auth()->user()->educationalSupervisorSubjects->pluck('id')->toArray())) {
            unauthorize();
        }
        $periodicTest->delete();
        return response()->json([
            'meta' => [
                'message' => trans('app.Deleted Successfully')
            ]
        ]);
    }


}
