<?php

namespace App\OurEdu\SchoolAdmin\GeneralQuizzesReports\TotalPercentage\Repositories;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TotalPercentageReportsRepository
{

    public function getUserQuizzes(User $user)
    {
        return  GeneralQuiz::query()
            ->where("school_account_id", "=", $user->schoolAdmin->current_school_id)
            ->whereNotNull("published_at")
            ->where("is_active", "=", 1)
            ->selectRaw("SUM(students_total_marks) as students_total_marks, COUNT(*) as count, SUM(mark) as total_marks, SUM(attend_students) as attend_students")
            ->first();

    }

    public function getSchoolStudents(User $user)
    {
        return Student::query()
            ->whereHas("classroom.branch", function (Builder $query) use ($user) {
                $query->where("school_account_id", "=", $user->schoolAdmin->current_school_id);
            })->count();
    }

    public function getBranches(User $user)
    {
        return SchoolAccountBranch::query()
            ->where("school_account_id", "=", $user->schoolAdmin->current_school_id)
            ->with(['generalQuizzes' => function (HasMany $generalQuiz) {
                $generalQuiz->whereNotNull("published_at");
                $generalQuiz->where("is_active", "=", 1);
                $generalQuiz->selectRaw("SUM(students_total_marks) as students_total_marks, COUNT(*) as count, SUM(mark) as total_marks, branch_id, SUM(attend_students) as attend_students")
                    ->groupBy("branch_id");
            }])
            ->get();
    }
}
