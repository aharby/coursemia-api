<?php

namespace App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Exports\ListHomeworksExport;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Exports\StudentHomeworkScoreExport;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Middlewares\checkInstructorBelongToCourseMiddleware;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Middlewares\checkInstructorHasHomeworkMiddleware;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Requests\CreateCourseHomeworkRequest;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Requests\UpdateCourseHomeworkRequest;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Transformers\CourseHomeworkTransformer;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\Transformers\PaginateStudentAnswer;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\UseCases\CreateCourseHomeworkUseCaseInterface;
use App\OurEdu\GeneralQuizzes\CourseHomework\Instructor\UseCases\UpdateCourseHomeworkUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Exports\GeneralQuizQuestionsScoresExport;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Requests\RetakeHomeworkRequest;
use App\OurEdu\GeneralQuizzes\Homework\Instructor\Transformers\HwStudentTransformer;
use App\OurEdu\GeneralQuizzes\Homework\UseCases\RetakeGeneralQuizUseCaseInterface;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionViewAsStudentTransformer\QuestionViewAsStudentTransformer;
use App\OurEdu\GeneralQuizzes\UseCases\ViewAsStudentUseCase\ViewAsStudentUseCaseInterface;
use App\OurEdu\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Swis\JsonApi\Client\Interfaces\ParserInterface;


class CourseHomeworkController extends BaseApiController
{

    public function __construct(
        private CreateCourseHomeworkUseCaseInterface  $createHomeworkUseCase,
        private UpdateCourseHomeworkUseCaseInterface  $updateHomeworkUseCase,
        private GeneralQuizRepositoryInterface        $generalQuizRepo,
        private ParserInterface                       $parserInterface,
        private ViewAsStudentUseCaseInterface         $viewAsStudentUseCase,
        private RetakeGeneralQuizUseCaseInterface     $retakeGeneralQuizUseCase,
        private GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository,
    )
    {
        $this->middleware(checkInstructorBelongToCourseMiddleware::class);
        $this->middleware(checkInstructorHasHomeworkMiddleware::class);
        $this->middleware('type:instructor');
    }

    public function store(CreateCourseHomeworkRequest $request, Course $course)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $useCase = $this->createHomeworkUseCase->createHomeWork($data, $course);
        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['homework'],
                '',
                new CourseHomeworkTransformer(),
                ResourceTypesEnums::COURSE_HOMEWORK,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function update(UpdateCourseHomeworkRequest $request, GeneralQuiz $courseHomework, Course $course)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $useCase = $this->updateHomeworkUseCase->updateHomeWork($courseHomework, $data, $course);
        if (isset($questionData['errors'])) {
            return formatErrorValidation($questionData['errors']);
        }
        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['homework'],
                '',
                new CourseHomeWorkTransformer(),
                ResourceTypesEnums::COURSE_HOMEWORK,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function index(Request $request)
    {
        $date = $request->input('date');
        $report = $request->has('report');
        $courseId = $request->input('course_id');
        $courseHomeworks = $this->generalQuizRepo->listInstructorGeneralQuizzes(
            Auth::user(),
            GeneralQuizTypeEnum::COURSE_HOMEWORK,
            null,
            null,
            $date,
            $report,
            $courseId
        );

        $meta = [];
        if ($report) {
            $meta = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.course-homework.instructor.get.export',
                    ['course' => $courseId]
                ),
                'label' => trans('app.get export'),
                'method' => 'GET',
                'key' => APIActionsEnums::EXPORT_HOMEWORK_REPORTS
            ];
        }
        return $this->transformDataModInclude(
            $courseHomeworks,
            "course",
            new CourseHomeWorkTransformer(),
            ResourceTypesEnums::COURSE_HOMEWORK,
            $meta
        );
    }

    public function delete(GeneralQuiz $courseHomework)
    {
        $useCase = $this->updateHomeworkUseCase->deleteCourseHomeWork($courseHomework);

        if ($useCase['status'] == 200) {

            return response()->json([
                'meta' => [
                    'message' => trans('app.Deleted Successfully')
                ]
            ]);
        } else {

            return formatErrorValidation($useCase);
        }

    }

    public function publish(GeneralQuiz $courseHomework)
    {
        $useCase = $this->updateHomeworkUseCase->publishCourseHomeWork($courseHomework);

        if ($useCase['status'] == 200) {

            return response()->json([
                'meta' => [
                    'message' => $useCase['message']
                ]
            ]);
        } else {

            return formatErrorValidation($useCase);
        }
    }

    public function ExportIndexData(Request $request)
    {
        $date = $request->input('date');
        $report = $request->has('report');
        $courseId = $request->input('course_id');

        $homeworks = $this->generalQuizRepo->listInstructorGeneralQuizzesWithoutPagination(
            Auth::user(),
            GeneralQuizTypeEnum::COURSE_HOMEWORK,
            null,
            null,
            $date,
            $report,
            $courseId
        );

        return Excel::download(new ListHomeworksExport($homeworks), "List-Course-Homework.xls");
    }

    public function listStudentsScores(GeneralQuiz $courseHomework)
    {
        $params['listScore'] = true;
        $params['students'] = $this->generalQuizRepo->getGeneralQuizStudents($courseHomework);
        return $this->transformDataModInclude(
            $courseHomework,
            'hwStudents',
            new CourseHomeWorkTransformer($params),
            ResourceTypesEnums::HOMEWORK
        );
    }

    public function exportStudentsGrades(GeneralQuiz $courseHomework)
    {
        $grades = $this->generalQuizRepo->getGeneralQuizStudentAnswers($courseHomework);

        return Excel::download(
            new GeneralQuizQuestionsScoresExport($grades, $courseHomework),
            //replace all dashes from title to avoid exceptions
            preg_replace('/\\\\|\//i', '', $courseHomework->title) . "-export-students_scores.xls"
        );
    }

    public function exportStudentsScores(GeneralQuiz $courseHomework)
    {
        $students = $courseHomework->students()->count() > 0 ?
            $courseHomework->students()->get() :
            $this->generalQuizRepo->students($courseHomework);

        return Excel::download(
            new StudentHomeworkScoreExport(
                $students, $courseHomework
            ),
            preg_replace('/\\\\|\//i', '', $courseHomework->title) . "_student_results.xls"
        );

    }

    public function show(GeneralQuiz $courseHomework)
    {
        return $this->transformDataModInclude(
            $courseHomework,
            "course",
            new CourseHomeWorkTransformer(),
            ResourceTypesEnums::COURSE_HOMEWORK
        );
    }

    public function preview(GeneralQuiz $courseHomework)
    {
        $page = request('page') ?? 1;
        $usecase = $this->viewAsStudentUseCase->nextOrBackQuestion($courseHomework->id, $page);
        if ($usecase['status'] == 200) {
            $params["student"] = Auth::user();
            $bankQuestions = $usecase['bankQuestions'];
            $courseHomework = $usecase['generalQuiz'];

            if (isset($usecase['last_question'])) {
                $params['finish_general_quiz'] = true;
            }
            return $this->transformDataModInclude(
                $bankQuestions,
                'questions',
                new QuestionViewAsStudentTransformer($courseHomework, $params),
                ResourceTypesEnums::HOMEWORK_QUESTION
            );
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function retake(RetakeHomeworkRequest $request, GeneralQuiz $courseHomework)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $useCase = $this->retakeGeneralQuizUseCase->retake($courseHomework, $data);

        if ($useCase['status'] == 200) {
            return $this->transformDataModInclude(
                $useCase['homework'],
                'classrooms',
                new CourseHomeWorkTransformer(),
                ResourceTypesEnums::HOMEWORK,
                $useCase['meta']
            );
        } else {
            return formatErrorValidation($useCase);
        }
    }

    public function getStudentHomeworkAnswers(GeneralQuiz $courseHomework, User $student)
    {
        $studentGeneralQuiz = $this->generalQuizStudentRepository->findStudentGeneralQuiz($courseHomework->id, $student->id);
        return $this->transformDataModInclude(
            $studentGeneralQuiz,
            'questions',
            new HwStudentTransformer($courseHomework, $student, true),
            ResourceTypesEnums::HOMEWORK_Student
        );
    }
    public function getStudentAnswersSolved(GeneralQuiz $courseHomework, User $student)
    {
        return $this->transformDataModInclude(
            ['data' => 'fale'],
            '',
            new PaginateStudentAnswer($courseHomework, $student),
            ResourceTypesEnums::HOMEWORK
        );
    }

}
