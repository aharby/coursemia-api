<?php


namespace App\OurEdu\GeneralQuizzes\Homework\Instructor\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Exports\GeneralQuizQuestionsScoresExport;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Exports\ListHomeworksExport;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Exports\StudentHomeworkScoreExport;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Requests\RetakeHomeworkRequest;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transforments\SubjectSectionTransformer;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\HomeworkTransformer;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\HwStudentTransformer;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\PaginateStudentAnswer;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\UseCases\InstructorHomeworkUseCases\CreateHomeworkUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\UseCases\InstructorHomeworkUseCases\UpdateHomeworkUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Homework\Requests\CreateHomeworkRequest;
use App\OurEdu\GeneralQuizzes\Homework\Requests\UpdateHomeworkRequest;
use App\OurEdu\GeneralQuizzes\Homework\UseCases\RetakeGeneralQuizUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Middlewares\checkInstructorMiddleware;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionViewAsStudentTransformer\QuestionViewAsStudentTransformer;
use App\OurEdu\GeneralQuizzes\UseCases\ViewAsStudentUseCase\ViewAsStudentUseCaseInterface;
use App\OurEdu\Users\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class HomeworkController extends BaseApiController
{
    private $generalQuizRepo;
    private $createHomeworkUseCase;
    private $updateHomeworkUseCase;
    private $parserInterface;
    private $generalQuizRepository;
    private $generalQuizStudentRepository;
    /**
     * @var RetakeGeneralQuizUseCaseInterface
     */
    private $retakeGeneralQuizUseCase;

    private $viewAsStudentUseCase;

    /**
     * HomeworkController constructor.
     * @param GeneralQuizRepositoryInterface $generalQuizRepo
     * @param CreateHomeworkUseCaseInterface $createHomeworkUseCase
     * @param UpdateHomeworkUseCaseInterface $updateHomeworkUseCase
     * @param ParserInterface $parserInterface
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     * @param GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository
     * @param RetakeGeneralQuizUseCaseInterface $retakeGeneralQuizUseCase
     * @param ViewAsStudentUseCaseInterface $viewAsStudentUseCase
     */
    public function __construct(
        GeneralQuizRepositoryInterface $generalQuizRepo,
        CreateHomeworkUseCaseInterface $createHomeworkUseCase,
        UpdateHomeworkUseCaseInterface $updateHomeworkUseCase,
        ParserInterface $parserInterface,
        GeneralQuizRepositoryInterface $generalQuizRepository,
        GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository,
        RetakeGeneralQuizUseCaseInterface $retakeGeneralQuizUseCase,
        ViewAsStudentUseCaseInterface $viewAsStudentUseCase
    ) {
        $this->generalQuizRepo = $generalQuizRepo;
        $this->generalQuizStudentRepository = $generalQuizStudentRepository;
        $this->createHomeworkUseCase = $createHomeworkUseCase;
        $this->updateHomeworkUseCase = $updateHomeworkUseCase;
        $this->parserInterface = $parserInterface;
//        $this->middleware('type:school_instructor|school_supervisor|academic_coordinator|school_leader');
//        $this->middleware(checkInstructorMiddleware::class)->only(['editHomeWork']);

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

        $homeworks = $this->generalQuizRepository->listInstructorGeneralQuizzes(
            Auth::user(),
            GeneralQuizTypeEnum::HOMEWORK,
            $subjectId,
            $gradeClassId,
            $date,
            $report
        );

        return $this->transformDataModInclude(
            $homeworks,
            "subject,gradeClass,classrooms",
            new HomeworkTransformer(),
            ResourceTypesEnums::HOMEWORK
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
            GeneralQuizTypeEnum::HOMEWORK,
            $subjectId,
            $gradeClassId,
            $date,
            $report
        );

        return Excel::download(new ListHomeworksExport($homeworks), "List-homework.xls");
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

    public function createHomeWork(
        CreateHomeworkRequest $request
    ) {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $useCase = $this->createHomeworkUseCase->createHomeWork($data);
        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['homework'],
                'classrooms',
                new HomeworkTransformer(),
                ResourceTypesEnums::HOMEWORK,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }


    public function editHomeWork(UpdateHomeworkRequest $request, $homeworkId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $useCase = $this->updateHomeworkUseCase->updateHomeWork($homeworkId, $data);
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


    public function getHomeworkSection(GeneralQuiz $homework)
    {
        return $this->transformDataMod(
            $homework->sections,
            new SubjectFormatSubjectTransformer(),
            ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
        );
    }

    public function publish(GeneralQuiz $homework)
    {
        if ($homework->created_by !== auth()->user()->id) {
            unauthorize();
        }

        $useCase = $this->updateHomeworkUseCase->publishHomework($homework);

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

    public function delete(GeneralQuiz $homework)
    {
        if ($homework->created_by !== auth()->user()->id) {
            unauthorize();
        }
        $homework->delete();

        return response()->json([
            'meta' => [
                'message' => trans('app.Deleted Successfully')
            ]
        ]);
    }

    public function listStudentsScores(GeneralQuiz $homework)
    {
        $params['listScore'] = true;
        $params['students'] = $this->generalQuizRepo->getGeneralQuizStudents($homework);
        return $this->transformDataModInclude(
            $homework,
            'hwStudents.classroom',
            new HomeworkTransformer($params),
            ResourceTypesEnums::HOMEWORK
        );
    }

    public function exportStudentsScores(GeneralQuiz $homework)
    {
        $students = $homework->students()->count() > 0 ?
            $homework->students()->get() :
            $this->generalQuizRepo->students($homework);

        return Excel::download(
            new StudentHomeworkScoreExport(
                $students, $homework
            ),
            preg_replace('/\\\\|\//i', '',$homework->title) . "_student_results.xls"
        );

    }

    public function getStudentHomeworkAnswers(GeneralQuiz $homework, User $student)
    {
        $studentGeneralQuiz = $this->generalQuizStudentRepository->findStudentGeneralQuiz($homework->id, $student->id);
        return $this->transformDataModInclude(
            $studentGeneralQuiz,
            'questions',
            new HwStudentTransformer($homework, $student),
            ResourceTypesEnums::HOMEWORK_Student
        );
    }

    /*
     * return pagination of student correct answer or not
     * */
    public function getStudentAnswersSolved(GeneralQuiz $homework, User $student)
    {
        return $this->transformDataModInclude(
            ['data' => 'fale'],
            '',
            new PaginateStudentAnswer($homework, $student),
            ResourceTypesEnums::HOMEWORK
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

    public function retake(RetakeHomeworkRequest $request, GeneralQuiz $homework)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $useCase = $this->retakeGeneralQuizUseCase->retake($homework, $data);

        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['homework'],
                'classrooms',
                new HomeworkTransformer(),
                ResourceTypesEnums::HOMEWORK,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function exportStudentsGrades(GeneralQuiz $homework)
    {
        $grades = $this->generalQuizRepository->getGeneralQuizStudentAnswers($homework);

        return Excel::download(
            new GeneralQuizQuestionsScoresExport($grades, $homework),
            //replace all dashes from title to avoid exceptions
            preg_replace('/\\\\|\//i', '', $homework->title) . "-export-students_scores.xls"
        );
    }
}
