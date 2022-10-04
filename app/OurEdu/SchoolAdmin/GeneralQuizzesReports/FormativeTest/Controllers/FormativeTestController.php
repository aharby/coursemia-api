<?php

namespace App\OurEdu\SchoolAdmin\GeneralQuizzesReports\FormativeTest\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\SchoolAdmin\GeneralQuizzesReports\FormativeTest\Exports\GeneralQuizQuestionsScoresExport;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAdmin\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\SchoolAdmin\GeneralQuizzesReports\FormativeTest\Exports\GeneralQuizStudentScoreExport;
use App\OurEdu\SchoolAdmin\GeneralQuizzesReports\FormativeTest\Exports\ListGeneralQuizzesExport;
use App\OurEdu\SchoolAdmin\GeneralQuizzes\Repositories\GeneralQuizRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use function response;
use function trans;
use function unauthorize;
use function view;

class FormativeTestController extends BaseController
{
    private $generalQuizRepository;

    public function __construct()
    {
        $this->generalQuizRepository = new GeneralQuizRepository();
    }

    public function index(Request $request)
    {
        $data = [];
        $requestData = $request->all();

        $user = Auth::user();
        $data["schools"]  = $user->schoolAdminAssignedSchools->pluck('name', 'id');

        $data["branches"] =[];

        if ($request->filled("school_id")) {
            $data["branches"] =   SchoolAccountBranch::query()->where('school_account_id', $requestData['school_id'])->pluck('name', 'id');
        }

        $data["page_title"] = trans('navigation.formative_test_report') ;
        $data['generalQuizzes'] = $this->generalQuizRepository->listSchoolAdminFormativeTest($requestData);

        return view("school_admin.GeneralQuizzesReports.formative_test.index", $data);
    }

    public function export(Request $request, SchoolAccountBranch $branch = null)
    {
        $requestData = $request->all();

        $generalQuizzes = $this->generalQuizRepository->listSchoolAdminFormativeTestWithoutPagination($requestData, $created_by = false);

        return Excel::download(new ListGeneralQuizzesExport($generalQuizzes), "List-general-quizzes.xls");
    }

    public function getSchoolBranches(SchoolAccount $schoolAccount = null )
    {
        $data = [];
        if($schoolAccount){
            $data = SchoolAccountBranch::query()->where('school_account_id',$schoolAccount->id)->pluck('name','id');
        }
        return response()->json([
            'data' => $data
        ]);
    }

    public function students(GeneralQuiz $generalQuiz)
    {
        $data['student_answered'] = $generalQuiz->studentsAnswered()->with('branch')->pluck('score', 'student_id')->toArray();
        $data['students'] = $this->generalQuizRepository->getGeneralQuizStudents($generalQuiz);
        $data['generalQuiz'] = $generalQuiz;
        $data["page_title"] = trans("navigation.quizzes");
        return view("school_admin.GeneralQuizzesReports.formative_test.students", $data);
    }

    public function exportStudents(GeneralQuiz $generalQuiz)
    {
        $data['student_answered'] = $generalQuiz->studentsAnswered()->with('branch')->pluck('score', 'student_id')->toArray();

        if ($generalQuiz->students()->count() > 0) {
            $students = $generalQuiz->students()->get();
        } else {
            $students = $this->generalQuizRepository->students($generalQuiz);
        }

        return Excel::download(
            new GeneralQuizStudentScoreExport($students, $generalQuiz),
            $generalQuiz->title . "-students.xls"
        );
    }

    public function exportStudentsGrades(GeneralQuiz $generalQuiz)
    {
        $grades = $this->generalQuizRepository->getGeneralQuizStudentAnswers($generalQuiz);

        return Excel::download(
            new GeneralQuizQuestionsScoresExport($grades, $generalQuiz),
            $generalQuiz->title . "-students_scores.xls"
        );
    }
}
