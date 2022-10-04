<?php

namespace App\OurEdu\SchoolAdmin\GeneralQuizzesReports\TotalPercentage\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAdmin\GeneralQuizzesReports\Exports\TotalPercentageReportExport;
use App\OurEdu\SchoolAdmin\GeneralQuizzesReports\TotalPercentage\Repositories\TotalPercentageReportsRepository;
use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class TotalPercentageReportsController extends BaseController
{
    private $repository;

    public function __construct(TotalPercentageReportsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getIndex()
    {
        $user = Auth::user();
        $schoolQuizzes = $this->repository->getUserQuizzes($user);
        $schoolStudents = $this->repository->getSchoolStudents($user);

        $percentage_average_scores = ($schoolQuizzes->attend_students > 0 and $schoolQuizzes->total_marks > 0 ) ? ($schoolQuizzes->students_total_marks / $schoolQuizzes->attend_students/$schoolQuizzes->total_marks)*100 : 0;


        $branches = $this->repository->getBranches($user);

        foreach ($branches as $branch) {
            $generalQuizCount = 0;
            $generalQuizScoreAverage = 0;

            if (count($branch->generalQuizzes)) {
                $generalQuizCount = $branch->generalQuizzes[0]->count;
                $generalQuizScoreAverage = ($branch->generalQuizzes[0]->attend_students > 0 and $branch->generalQuizzes[0]->total_marks > 0) ? ($branch->generalQuizzes[0]->students_total_marks/$branch->generalQuizzes[0]->attend_students/$branch->generalQuizzes[0]->total_marks)*100 : 0;
            }

            $branch->general_quizzes_count = $generalQuizCount;
            $branch->general_quizzes_score_average = number_format($generalQuizScoreAverage, "2", '.', '');
        }

        $data["percentage_average_scores"] = number_format($percentage_average_scores, 2, '.', '');
        $data["quizzes_count"] = $schoolQuizzes->count;
        $data["school_students"] = $schoolStudents;
        $data['branches'] = $branches;
        $data['page_title'] = trans('navigation.total percentages report');

        return view("school_admin.GeneralQuizzesReports.total_percentages_report", $data);
    }
    public function totalPercentagesChartReport()
    {
        $user = Auth::user();
        $schoolQuizzes = $this->repository->getUserQuizzes($user);
        $schoolStudents = $this->repository->getSchoolStudents($user);

        $percentage_average_scores = ($schoolQuizzes->attend_students > 0 and $schoolQuizzes->total_marks > 0 ) ? ($schoolQuizzes->students_total_marks / $schoolQuizzes->attend_students/$schoolQuizzes->total_marks)*100 : 0;


        $branches = $this->repository->getBranches($user);


        foreach ($branches as $branch) {
            $generalQuizCount = 0;
            $generalQuizScoreAverage = 0;

            if (count($branch->generalQuizzes)) {
                $generalQuizCount = $branch->generalQuizzes[0]->count;
                $generalQuizScoreAverage = ($branch->generalQuizzes[0]->attend_students > 0 and $branch->generalQuizzes[0]->total_marks > 0) ? ($branch->generalQuizzes[0]->students_total_marks/$branch->generalQuizzes[0]->attend_students/$branch->generalQuizzes[0]->total_marks)*100 : 0;
            }

            $branch->general_quizzes_count = $generalQuizCount;
            $branch->general_quizzes_score_average = number_format($generalQuizScoreAverage, "2", '.', '');
        }

        $data["percentage_average_scores"] = number_format($percentage_average_scores, 2, '.', '');
        $data["quizzes_count"] = $schoolQuizzes->count;
        $data["school_students"] = $schoolStudents;
        $data['branches'] = $branches;

        $labels = [];
        $colors = [];
        $percentagesData = [];
        foreach($branches as $branch) {
            $labels[] = $branch->name;
            $colors[] = "rgb(".floor($branch->id%256). ", ". floor($branch->id/256%256). ", ". floor($branch->id/256/256%256) . "})";
            $percentagesData[] = $branch->general_quizzes_score_average;
        }

        $data['labels'] = $labels;
        $data['colors'] = $colors;
        $data['percentagesData'] = $percentagesData;
        $data['page_title'] = trans("navigation.total percentages report");

        return view("school_admin.GeneralQuizzesReports.total_percentages_report_chart", $data);
    }

    public function totalPercentagesReportExport()
    {
        $user = Auth::user();

        $branches = $this->repository->getBranches($user);

        foreach ($branches as $branch) {
            $generalQuizCount = 0;
            $generalQuizScoreAverage = 0;

            if (count($branch->generalQuizzes)) {
                $generalQuizCount = $branch->generalQuizzes[0]->count;
                $generalQuizScoreAverage = ($branch->generalQuizzes[0]->attend_students > 0 and $branch->generalQuizzes[0]->total_marks > 0) ? ($branch->generalQuizzes[0]->students_total_marks/$branch->generalQuizzes[0]->attend_students/$branch->generalQuizzes[0]->total_marks)*100 : 0;
            }

            $branch->general_quizzes_count = $generalQuizCount;
            $branch->general_quizzes_score_average = number_format($generalQuizScoreAverage, "2", '.', '');
        }

        return Excel::download(new TotalPercentageReportExport($branches), "total percentages Report.xls");
    }
}
