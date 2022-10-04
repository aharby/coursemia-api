<?php

namespace App\OurEdu\SchoolAdmin\GeneralQuizzesReports\BranchesReports\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\SchoolAdmin\GeneralQuizzesReports\Exports\SectionPercentageReportExport;
use App\OurEdu\SchoolAdmin\GeneralQuizzesReports\Exports\SkillPercentageLevelReportExport;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class SkillsLevelReportsControllers extends BaseController
{
    private $userSchool;
    /**
     * @var GeneralQuizRepositoryInterface
     */
    private $generalQuizRepository;

    /**
     * @var SubjectRepositoryInterface
     */
    private $subjectRepository;

    /**
     * ReportsController constructor.
     * @param GeneralQuizRepositoryInterface $generalQuizRepository
     */
    public function __construct(GeneralQuizRepositoryInterface $generalQuizRepository, SubjectRepositoryInterface $subjectRepository)
    {
        $this->generalQuizRepository = $generalQuizRepository;
        $this->subjectRepository = $subjectRepository;
    }

    public function skillPercentageLevelReport(Request $request){
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs = $user->schoolAdmin->currentSchool->branches()->pluck('id')->toArray();

        $branch = SchoolAccountBranch::query()->where("id", "=", $request->get("branch_id"))->first();

        if ($branch and !in_array($branch->id, $branchesIDs)) {
            return unauthorize();
        }

        if (!$branch) {
            $requestData['branch_id'] = $branchesIDs;
        }
        $data["gradeClasses"] = [];
        if ($branch) {
            $data["gradeClasses"] = $branch->branchEducationalSystem()->with('gradeClasses.translations')->get()->pluck('gradeClasses')->flatten();
        }

        $subjects = $classrooms =[];

        if ($request->filled("gradeClass")) {
            $gradeClass = GradeClass::query()
                ->where("id", "=", $request->get('gradeClass'))
                ->first();

            $subjects = $this->subjectRepository->getGradeBranchSubjectsPluck($branch, $gradeClass);

            $classrooms = $branch
                ->classrooms()
                ->whereHas("branchEducationalSystemGradeClass", function (Builder $branchEducationSystemGrade) use ($request) {
                    $branchEducationSystemGrade->where("grade_class_id", "=", $request->get("gradeClass"));
                })->pluck("name", "id");
        }


        $data["branches"] = $user->schoolAdmin->currentSchool->branches()->pluck('name' , 'id')->toArray();
        $data["quizTypes"] = GeneralQuizTypeEnum::getAllQuizTypes();
        $data["subjects"] = $subjects;
        $data["classrooms"] = $classrooms;
        $data["page_title"] = trans("navigation.Skill Level Report");
        $data['generalQuizzes'] = $this->generalQuizRepository->listGeneralQuizzes($requestData);
        return view("school_admin.GeneralQuizzesReports.branch_reports.skill-level.index", $data);
    }

    public function skillPercentageLevelReportExport(Request $request){
        $requestData = $request->all();

        $user = Auth::user();
        $branchesIDs = $user->schoolAdmin->currentSchool->branches()->pluck('id')->toArray();

        $branch = SchoolAccountBranch::query()->where("id", "=", $request->get("branch_id"))->first();

        if ($branch and !in_array($branch->id, $branchesIDs)) {
            return unauthorize();
        }

        if (!$branch) {
            $requestData['branch_id'] = $branchesIDs;
        }

        $generalQuizzes = $this->generalQuizRepository->listGeneralQuizzesWithoutPagination($requestData);

        return Excel::download(new SkillPercentageLevelReportExport($generalQuizzes), "skill percentage report.xls");
    }

    public function sectionPercentageReport(GeneralQuiz $generalQuiz)
    {
        $sections = GeneralQuizStudentAnswer::where('general_quiz_id',$generalQuiz->id)
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

        $sectionGrades = GeneralQuizQuestionBank::with('section')->whereHas('generalQuiz',function($query)use($generalQuiz){
            $query->where('general_quiz_id',$generalQuiz->id);
        })->get()->groupBy('section.title');
        $data['sectionGrades'] = $sectionGrades;
        $data['sections'] = $sections;
        $data['generalQuiz'] = $generalQuiz;
        $data["page_title"] = trans("navigation.Skill Level Report");
        return view("school_admin.GeneralQuizzesReports.branch_reports.skill-level.skills", $data);
    }

    public function sectionPercentageReportExport(GeneralQuiz $generalQuiz)
    {
        $sections = GeneralQuizStudentAnswer::where('general_quiz_id',$generalQuiz->id)
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

        $sectionGrades = GeneralQuizQuestionBank::with('section')->whereHas('generalQuiz',function($query)use($generalQuiz){
            $query->where('general_quiz_id',$generalQuiz->id);
        })->get()->groupBy('section.title');

        return Excel::download(new SectionPercentageReportExport($sections, $generalQuiz, $sectionGrades), "section percentage.xls");
    }

    public function sectionPercentageReportChart(GeneralQuiz $generalQuiz)
    {
        $answers = GeneralQuizStudentAnswer::where('general_quiz_id',$generalQuiz->id)
            ->with(['section'=>function($query){
                $query->select('id','title');
            }])
            ->select(
                'student_id',
                'subject_format_subject_id',
                DB::raw('SUM(score) as total_score'),
            // DB::raw('AVG(score) as score_average') ,
            // DB::raw('count(*) as total_questions'),
            )
            ->groupBy('subject_format_subject_id','student_id')
            ->get();
        $sections = $answers->groupBy('section.title');


        $sectionGrades = GeneralQuizQuestionBank::with('section')->whereHas('generalQuiz',function($query)use($generalQuiz){
            $query->where('general_quiz_id',$generalQuiz->id);
        })->get()->groupBy('section.title');

        $data['sectionGrades'] = $sectionGrades;
        $data['sections'] = $sections;
        $data['generalQuiz'] = $generalQuiz;
        $data["page_title"] = trans("navigation.Skill Level Report");
        return view("school_admin.GeneralQuizzesReports.branch_reports.skill-level.skills_charts", $data);
    }
}
