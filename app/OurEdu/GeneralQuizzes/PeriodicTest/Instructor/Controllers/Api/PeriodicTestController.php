<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Exports\GeneralQuizQuestionsScoresExport;
use App\OurEdu\GeneralQuizzes\Middlewares\checkInstructorMiddleware;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Exports\ListPeriodicTestExport;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Exports\StudentPeriodicTestScoreExport;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Requests\CreatePeriodicTestRequest;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Requests\PublishPeriodicTestRequest;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Requests\RetakePeriodicTestRequest;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Requests\UpdatePeriodicTestRequest;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Transformers\PaginateStudentAnswer;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Transformers\PeriodicTestStudentTransformer;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Transformers\PeriodicTestTransformer;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Transformers\QuestionViewAsStudentTransformer;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\UseCases\CreatePeriodicTestUseCase\CreatePeriodicTestUseCaseInterface;
use App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\UseCases\UpdatePeriodicTestUseCase\UpdatePeriodicTestUseCaseInterface;
use App\OurEdu\GeneralQuizzes\PeriodicTest\UseCases\RetakeGeneralQuizUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use App\OurEdu\GeneralQuizzes\UseCases\ViewAsStudentUseCase\ViewAsStudentUseCaseInterface;
use App\OurEdu\Users\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class PeriodicTestController extends BaseApiController
{
    private $generalQuizRepo;
    private $createPeriodicTestUseCase;
    private $updatePeriodicTestUseCase;
    private $parserInterface;
    private $generalQuizRepository;
    private $generalQuizStudentRepository;
    private $retakeGeneralQuizUseCase;
    /**
     * @var ViewAsStudentUseCaseInterface
     */
    private $viewAsStudentUseCase;

    /**
     * PeriodicTestController constructor.
     * @param GeneralQuizRepositoryInterface $generalQuizRepo
     * @param CreatePeriodicTestUseCaseInterface $createPeriodicTestUseCase
     * @param UpdatePeriodicTestUseCaseInterface $updatePeriodicTestUseCase
     * @param ParserInterface $parserInterface
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     * @param GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository
     * @param RetakeGeneralQuizUseCaseInterface $retakeGeneralQuizUseCase
     * @param ViewAsStudentUseCaseInterface $viewAsStudentUseCase
     */
    public function __construct(
        GeneralQuizRepositoryInterface $generalQuizRepo,
        CreatePeriodicTestUseCaseInterface $createPeriodicTestUseCase,
        UpdatePeriodicTestUseCaseInterface $updatePeriodicTestUseCase,
        ParserInterface $parserInterface,
        GeneralQuizRepositoryInterface $generalQuizRepository,
        GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository,
        RetakeGeneralQuizUseCaseInterface $retakeGeneralQuizUseCase,
        ViewAsStudentUseCaseInterface $viewAsStudentUseCase
    ) {
        $this->generalQuizRepo = $generalQuizRepo;
        $this->generalQuizStudentRepository = $generalQuizStudentRepository;
        $this->createPeriodicTestUseCase = $createPeriodicTestUseCase;
        $this->updatePeriodicTestUseCase = $updatePeriodicTestUseCase;
        $this->parserInterface = $parserInterface;
//        $this->middleware('type:school_instructor|school_supervisor|academic_coordinator|school_leader');
//        $this->middleware(checkInstructorMiddleware::class)->only(['editPeriodicTest']);
        $this->generalQuizRepository = $generalQuizRepository;
        $this->retakeGeneralQuizUseCase = $retakeGeneralQuizUseCase;
        $this->viewAsStudentUseCase = $viewAsStudentUseCase;
    }


    public function index()
    {
        $subjectId = request()->input('subject_id');
        $gradeClassId = request()->input('grade_class_id');
        $date = request()->input('date');
        $report = request()->has('report');

        $periodicTests =
            $this->generalQuizRepository->listInstructorGeneralQuizzes(
                Auth::user(),
                GeneralQuizTypeEnum::PERIODIC_TEST,
                $subjectId,
                $gradeClassId,
                $date,
                $report
            );

        return $this->transformDataModInclude(
            $periodicTests,
            "subject,gradeClass,classrooms",
            new PeriodicTestTransformer(),
            ResourceTypesEnums::Periodic_Test
        );
    }

    public function ExportIndexData()
    {
        $subjectId = request()->input('subject_id');
        $gradeClassId = request()->input('grade_class_id');
        $date = request()->input('date');
        $report = request()->has('report');

        $homeworks = $this->generalQuizRepository->listInstructorGeneralQuizzesWithoutPagination(
            Auth::user(),
            GeneralQuizTypeEnum::PERIODIC_TEST,
            $subjectId,
            $gradeClassId,
            $date,
            $report
        );

        return Excel::download(new ListPeriodicTestExport($homeworks), "List-periodic.xls");
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

    public function createPeriodicTest(CreatePeriodicTestRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $useCase = $this->createPeriodicTestUseCase->createPeriodicTest($data);
        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['periodicTest'],
                'classrooms',
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
        $useCase = $this->updatePeriodicTestUseCase->updatePeriodicTest($periodicTestId, $data);
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


    public function getPeriodicTestSection(GeneralQuiz $periodicTest)
    {
        return $this->transformDataMod(
            $periodicTest->sectionsRelations,
            new SubjectFormatSubjectTransformer(),
            ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
        );
    }


    public function publish(GeneralQuiz $periodicTest, PublishPeriodicTestRequest $request)
    {
        if ($periodicTest->created_by !== auth()->user()->id) {
            unauthorize();
        }

        $useCase = $this->updatePeriodicTestUseCase->publishPeriodicTest($periodicTest);

        if ($useCase['status'] == 200) {
            return response()->json([
                'meta' => [
                    'message' => trans('api.Published Successfully')
                ]
            ]);
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function delete(GeneralQuiz $periodicTest)
    {
        if ($periodicTest->created_by !== auth()->user()->id) {
            unauthorize();
        }
        $periodicTest->delete();
        return response()->json([
            'meta' => [
                'message' => trans('app.Deleted Successfully')
            ]
        ]);
    }

    public function listStudentsScores(GeneralQuiz $periodicTest)
    {
        $params['listScore'] = true;
        $params['students'] = $this->generalQuizRepo->getGeneralQuizStudents($periodicTest);
        return $this->transformDataModInclude(
            $periodicTest,
            'periodicTestStudents',
            new PeriodicTestTransformer($params),
            ResourceTypesEnums::Periodic_Test
        );
    }

    public function exportStudentsScores(GeneralQuiz $periodicTest)
    {

        $students = $periodicTest->students()->count() > 0 ?
            $periodicTest->students()->get() :
            $this->generalQuizRepo->students($periodicTest);

        return Excel::download(
            new StudentPeriodicTestScoreExport(
                $students, $periodicTest
            ),
           preg_replace('/\\\\|\//i', '', $periodicTest->title) . "_student_results.xls"
        );
    }

    public function getStudentPeriodicTestAnswers(GeneralQuiz $periodicTest, User $student)
    {
        $studentGeneralQuiz = $this->generalQuizStudentRepository->findStudentGeneralQuiz(
            $periodicTest->id,
            $student->id
        );
        return $this->transformDataModInclude(
            $studentGeneralQuiz,
            'questions',
            new PeriodicTestStudentTransformer($periodicTest, $student),
            ResourceTypesEnums::Periodic_Test_Student
        );
    }


    public function getStudentAnswersSolved(GeneralQuiz $periodicTest, User $student)
    {
        return $this->transformDataModInclude(
            ['data' => 'fale'],
            '',
            new PaginateStudentAnswer($periodicTest, $student),
            ResourceTypesEnums::Periodic_Test
        );
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

    public function retake(RetakePeriodicTestRequest $request, GeneralQuiz $periodicTest)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $useCase = $this->retakeGeneralQuizUseCase->retake($periodicTest, $data);

        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['periodicTest'],
                'classrooms',
                new PeriodicTestTransformer(),
                ResourceTypesEnums::Periodic_Test,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function exportStudentsGrades(GeneralQuiz $periodicTest)
    {
        $grades = $this->generalQuizRepository->getGeneralQuizStudentAnswers($periodicTest);

        return Excel::download(
            new GeneralQuizQuestionsScoresExport($grades, $periodicTest),
            //replace all dashes from title to avoid exceptions
            preg_replace('/\\\\|\//i', '', $periodicTest->title) . "-export-students_scores.xls"
        );
    }
}
