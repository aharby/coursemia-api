<?php


namespace App\OurEdu\GeneralQuizzesReports\SchoolSupervisor\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;

class StudentLevelReportsChartsController extends BaseController
{
    /**
     * @var GeneralQuizRepositoryInterface
     */
    private $generalQuizRepository;

    /**
     * StudentLevelReportsChartsController constructor.
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     */
    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepository)
    {
        $this->generalQuizRepository = $generalQuizRepository;
    }

    public function studentQuizzes(User $student,Request $request)
    {
        $requestData = $request->all();
        $user = Auth::user();
        $branch = $user->branch;
        $requestData['branch_id'] = $branch->id;

        $generalQuizzes = $this->generalQuizRepository->listGeneralQuizzes($requestData,true)
            ->whereHas('studentsAnswered',function($query) use($student){
                $query->where('student_id','=',$student->id);
            })->pluck('id')->toArray();

        $studentQuizzes = GeneralQuizStudent::query()
            ->with("generalQuiz", "subject")
            ->whereIn('general_quiz_id',$generalQuizzes)
            ->where('student_id','=',$student->id)
            ->paginate();

        $data['studentQuizzes'] = $studentQuizzes;
        $data["page_title"] = $student->name;

        return view("school_supervisor.generalQuizReports.student_level.student_quizzes_charts", $data);
    }

    public function studentSectionPerformance(GeneralQuizStudent $generalQuizStudent)
    {
        $studentSectionsGrade = 
            GeneralQuizStudentAnswer::query()
                ->where('general_quiz_id',$generalQuizStudent->general_quiz_id)
                ->with([
                    'section'=>function($query){
                        $query->select('id','title');
                    }])->where("student_id", "=", $generalQuizStudent->student_id)
                ->select(
                    'student_id',
                    'subject_format_subject_id',
                    DB::raw('SUM(score) as total_score'),
                )
                ->groupBy('subject_format_subject_id','student_id')
                ->get()->groupBy('section.title');
        
    
        $sectionsStudentsGrade = GeneralQuizStudentAnswer::query()
            ->where('general_quiz_id',$generalQuizStudent->general_quiz_id)
            ->with([
                'section'=>function($query){
                $query->select('id','title');
            }])->select(
                'student_id',
                'subject_format_subject_id',
                DB::raw('SUM(score) as total_score'),
            )
            ->groupBy('subject_format_subject_id','student_id')
            ->get()->groupBy('section.title');
    
        $sectionGrades = GeneralQuizQuestionBank::with('section')
            ->whereHas('generalQuiz',function($query)use($generalQuizStudent){
                $query->where('general_quiz_id',$generalQuizStudent->general_quiz_id);
            })->get()->groupBy('section.title');
    
        $data["generalQuizStudent"] = $generalQuizStudent;
        $data["studentSectionsGrade"] = $studentSectionsGrade;
        $data["sectionsStudentsGrade"] = $sectionsStudentsGrade;
        $data["sectionGrades"] = $sectionGrades;
    
        return view("school_supervisor.generalQuizReports.student_level.student_sections_charts", $data);
    }
}
