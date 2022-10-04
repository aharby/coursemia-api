<?php


namespace App\OurEdu\GeneralQuizzes\Homework\EducationalSupervisor\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Homework\EducationalSupervisor\Requests\UpdateHomeworkRequest;
use App\OurEdu\GeneralQuizzes\Homework\EducationalSupervisor\Transformers\HomeworkTransformer;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\UseCases\InstructorHomeworkUseCases\UpdateHomeworkUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Middlewares\EducationalSupervisorMiddleware;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionViewAsStudentTransformer\QuestionViewAsStudentTransformer;
use App\OurEdu\GeneralQuizzes\UseCases\ViewAsStudentUseCase\ViewAsStudentUseCaseInterface;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class HomeworkController extends BaseApiController
{
    private $parserInterface;
    private $generalQuizRepository;
    private $updateHomeworkUseCase;
    /**
     * @var ViewAsStudentUseCaseInterface
     */
    private ViewAsStudentUseCaseInterface $viewAsStudentUseCase;

    /**
     * HomeworkController constructor.
     * @param ParserInterface $parserInterface
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     * @param UpdateHomeworkUseCaseInterface $updateHomeworkUseCase
     * @param ViewAsStudentUseCaseInterface $viewAsStudentUseCase
     */
    public function __construct(
        ParserInterface $parserInterface,
        GeneralQuizRepositoryInterface $generalQuizRepository,
        UpdateHomeworkUseCaseInterface $updateHomeworkUseCase,
        ViewAsStudentUseCaseInterface $viewAsStudentUseCase
    ) {
        $this->generalQuizRepository = $generalQuizRepository;
        $this->updateHomeworkUseCase = $updateHomeworkUseCase;
        $this->parserInterface = $parserInterface;
        $this->viewAsStudentUseCase = $viewAsStudentUseCase;
        $this->middleware('type:educational_supervisor');
        $this->middleware(EducationalSupervisorMiddleware::class)->only(['editHomework', 'deactivateHomework']);
    }

    public function index()
    {
        $this->setFilters();

        $date = request()->input('date');
        $instructor = request()->input('instructor');

        $homeworks = $this->generalQuizRepository->listEducationalSupervisorGeneralQuizzes(
            Auth::user(),
            $this->filters,
            request()->get('classroom_id'),
            GeneralQuizTypeEnum::HOMEWORK,
            $instructor,
            $date
        );

        return $this->transformDataModInclude(
            $homeworks,
            "gradeClass,subject",
            new HomeworkTransformer(),
            ResourceTypesEnums::HOMEWORK
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


    public function show(GeneralQuiz $homework)
    {
        return $this->transformDataModInclude(
            $homework,
            'classrooms,classroomStudents',
            new HomeworkTransformer(),
            ResourceTypesEnums::HOMEWORK
        );
    }

    public function editHomeWork(UpdateHomeworkRequest $request, GeneralQuiz $homework)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $useCase = $this->updateHomeworkUseCase->updateHomeWork($homework->id, $data);
        if (isset($questionData['errors'])) {
            return formatErrorValidation($questionData['errors']);
        }
        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['homework'],
                'classrooms,classroomStudents',
                new HomeworkTransformer(),
                ResourceTypesEnums::HOMEWORK,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }


    public function deactivateHomework(GeneralQuiz $homework)
    {
        $useCase = $this->updateHomeworkUseCase->deactivateHomework($homework);

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

    public function getHomeworkSection(GeneralQuiz $homework)
    {
        return $this->transformDataMod(
            $homework->sections,
            new SubjectFormatSubjectTransformer(),
            ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
        );
    }

    public function preview(GeneralQuiz $homework)
    {
        $page = request('page') ?? 1;
        $usecase = $this->viewAsStudentUseCase->nextOrBackQuestion($homework->id, $page);
        if ($usecase['status'] == 200) {
            $params["student"] = Auth::user();
            $bankQuestions = $usecase['bankQuestions'];
            $homework = $usecase['generalQuiz'];

            if (isset($usecase['last_question'])) {
                $params['finish_general_quiz'] = true;
            }
            return $this->transformDataModInclude(
                $bankQuestions,
                'questions',
                new QuestionViewAsStudentTransformer($homework, $params),
                ResourceTypesEnums::HOMEWORK_QUESTION
            );
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function delete(GeneralQuiz $homework)
    {
        if (!in_array($homework->branch_id, \auth()->user()->branches->pluck('id')->toArray()) ||
            !in_array($homework->subject_id, \auth()->user()->educationalSupervisorSubjects->pluck('id')->toArray())) {
            unauthorize();
        }
        $homework->delete();
        return response()->json([
            'meta' => [
                'message' => trans('app.Deleted Successfully')
            ]
        ]);
    }

}
