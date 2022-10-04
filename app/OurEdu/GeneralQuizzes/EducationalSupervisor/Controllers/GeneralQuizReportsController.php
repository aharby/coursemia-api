<?php


namespace App\OurEdu\GeneralQuizzes\EducationalSupervisor\Controllers;


use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\EducationalSupervisor\Transformers\GeneralQuizReportTransformer;
use App\OurEdu\GeneralQuizzes\EducationalSupervisor\Transformers\PaginateStudentAnswerTransformer;
use App\OurEdu\GeneralQuizzes\EducationalSupervisor\Transformers\StudentsQuestionsTransformer;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Exports\GeneralQuizQuestionsScoresExport;
use App\OurEdu\GeneralQuizzes\Exports\ListGeneralQuizzesExport;
use App\OurEdu\GeneralQuizzes\Exports\StudentGeneralQuizzesScoreExport;
use App\OurEdu\GeneralQuizzes\Homework\EducationalSupervisor\Transformers\HomeworkTransformer;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepository;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class GeneralQuizReportsController extends BaseApiController
{
    /**
     * @var GeneralQuizRepositoryInterface
     */
    private GeneralQuizRepositoryInterface $generalQuizRepository;

    private GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository;


    /**
     * GeneralQuizReportsController constructor.
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     * @param GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository
     */
    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepository , GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository)
    {
        $this->generalQuizRepository = $generalQuizRepository;
        $this->generalQuizStudentRepository = $generalQuizStudentRepository;
    }

    public function index()
    {
        $this->setFilters();

        $date = request()->input('date');
        $instructor = request()->input('instructor');

        $homeworks = $this->generalQuizRepository->listEducationalSupervisorGeneralQuizzes(
            Auth::user(), $this->filters,request()->get('classroom_id'), request()->get('quiz_type'), $instructor, $date, true, request()->all()
        );

        return $this->transformDataModInclude($homeworks, "Instructor ,Classrooms,branch,actions", new GeneralQuizReportTransformer(), ResourceTypesEnums::GENERAL_QUIZ);
    }

    public function listStudentsScores(GeneralQuiz $generalQuiz)
    {

        $params['listScore'] = true;
        $params['students'] = $this->generalQuizRepository->getGeneralQuizStudents($generalQuiz);

        return $this->transformDataModInclude(
            $generalQuiz,
            'students',
            new GeneralQuizReportTransformer($params),
            ResourceTypesEnums::GENERAL_QUIZ
        );
    }



    public function  exportStudentsGrades(GeneralQuiz $generalQuiz)
    {
        $grades = $this->generalQuizRepository->getGeneralQuizStudentAnswers($generalQuiz);

        return Excel::download(
            new GeneralQuizQuestionsScoresExport($grades, $generalQuiz),
            //replace all dashes from title to avoid exceptions
            preg_replace('/\\\\|\//i', '', $generalQuiz->title) . "-export-students_scores.xls"
        );
    }

    /*
     * return pagination of student correct answer or not
     * */
    public function getStudentAnswersPaginates(GeneralQuiz $generalQuiz, User $student)
    {
        return $this->transformDataModInclude(['data'=>'fale'],'',new PaginateStudentAnswerTransformer($generalQuiz,$student),ResourceTypesEnums::GENERAL_QUIZ);
    }

    public function getStudentAnswers(GeneralQuiz $generalQuiz,User $student)
    {
        $studentGeneralQuiz=$this->generalQuizStudentRepository->findStudentGeneralQuiz($generalQuiz->id,$student->id);

        return $this->transformDataModInclude($studentGeneralQuiz, 'questions', new StudentsQuestionsTransformer($generalQuiz,$student), ResourceTypesEnums::HOMEWORK_Student);
    }
    public function ExportIndexData()
    {
        $this->setFilters();

        $date = request()->input('date');
        $instructor = request()->input('instructor');

        $homeworks = $this->generalQuizRepository->listEducationalSupervisorGeneralQuizzes(
            Auth::user(), $this->filters,request()->get('classroom_id'), request()->get('quiz_type'), $instructor, $date , false , request()->all()
        );

        return Excel::download(new ListGeneralQuizzesExport($homeworks), "List-homework.xls");
    }

    public function exportStudentsScores(GeneralQuiz $generalQuiz)
    {
        $students = $generalQuiz->students()->count() > 0 ?
            $generalQuiz->students()->get() :
            $this->generalQuizRepository->students($generalQuiz);

        return Excel::download(
            new StudentGeneralQuizzesScoreExport(
                $students, $generalQuiz
            ),
            $generalQuiz->title . '_student_results.xls'
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

        $subjects = $eduSupervisor->educationalSupervisorSubjects->pluck('name','id')->toArray();
        $this->filters[] = [
            'name' => 'subject_id',
            'type' => 'id',
            'data' => $subjects,
            'trans' => false,
            'value' => request()->get('subject_id'),
        ];

        $gradeClasses = $eduSupervisor->educationalSupervisorSubjects->pluck('gradeClass.title','gradeClass.id')->toArray();
        $this->filters[] = [
            'name' => 'grade_class_id',
            'type' => 'id',
            'data' => $gradeClasses,
            'trans' => false,
            'value' => request()->get('grade_class_id'),
        ];
    }
}
