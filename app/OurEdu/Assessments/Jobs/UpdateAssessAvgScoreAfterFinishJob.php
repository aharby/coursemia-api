<?php


namespace App\OurEdu\Assessments\Jobs;


use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAssessAvgScoreAfterFinishJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Assessment
     */
    private $assessment;

    /**
     * UpdateAssessAvgScoreAfterFinishJob constructor.
     * @param Assessment $assessment
     */
    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
    }

    public function handle()
    {
        $userFinishedAssessAvgScore = $this->assessment
            ->assessmentUsers()
            ->finished()
            ->average('score');

        $averageTotalMark = $this->assessment
            ->assessmentUsers()
            ->finished()
            ->average('total_mark');

        $this->assessment->average_score = $userFinishedAssessAvgScore ?? 0.00;
        $this->assessment->average_total_mark = $averageTotalMark ?? 0.00;
        $this->assessment->save();

        foreach ($this->assessment->resultViewers as $viewer) {
            $this->assessment->resultViewers()
                ->where("users.id", "=", $viewer->id)
                ->updateExistingPivot($viewer->id, ["average_score" => number_format($this->getViewerResultAssessmentRate($viewer), 2)]);
        }

    }

    private function getViewerResultAssessmentRate(User $viewer) :float
    {
        $branches = $this->getUserSchoolBranches($viewer);

        $assessmentUser = AssessmentUser::query()
            ->where("assessee_id", "!=", $viewer->id)
            ->where("assessment_id", "=", $this->assessment->id)
            ->finished()
            ->whereHas('assessor',function($assessorQuery) use($branches){
                $assessorQuery->whereHas("branches", function (Builder $builder) use ($branches) {
                    $builder->whereIn("school_account_branches.id", $branches);
                })->orWhereHas('schoolAccount.branches',function(Builder $builder)use($branches){
                    $builder->whereIn("school_account_branches.id", $branches);
                })->orWhereIn('branch_id',$branches);
        });

        $functionName = $viewer->type."AssessmentRate";

        if (method_exists($this, $functionName)) {
            return $this->{$functionName}($viewer, $assessmentUser);
        }

        return $assessmentUser->average("score") ?? 0.00;
    }

    private function educational_supervisorAssessmentRate(User $viewer, Builder $assessmentUser) :float
    {
        $subjects = $viewer->educationalSupervisorSubjects()->pluck('subjects.id')->toArray();

        $assessmentUserAverage = $assessmentUser
            ->where('assessment_id', "=", $this->assessment->id)
            ->whereHas("assessor", function (Builder $assessor) use ($subjects) {
                $assessor->where(function(Builder $query) use ($subjects) {
                    $query->where(function(Builder $q)use($subjects){
                        $q->where('type', UserEnums::EDUCATIONAL_SUPERVISOR)
                            ->whereHas('educationalSupervisorSubjects', function(Builder $assessorQuery) use($subjects){
                                $assessorQuery->whereIn('subject_id', $subjects);
                            });
                    })->orWhere(function(Builder $q) use ($subjects) {
                        $q->where('type', '=', UserEnums::SCHOOL_INSTRUCTOR)
                            ->whereHas('schoolInstructorSubjects', function(Builder $assessorQuery) use ($subjects){
                                $assessorQuery->whereIn('subject_id', $subjects);
                            });
                    })
                        ->orWhereNotIn('type',[UserEnums::SCHOOL_INSTRUCTOR,UserEnums::EDUCATIONAL_SUPERVISOR]);
                });
            })
            ->average("score");

        return $assessmentUserAverage ?? 0.00;
    }

    private function school_instructorAssessmentRate(User $viewer, Builder $assessmentUser) :float
    {
        $instructorSubjects = $viewer->schoolInstructorSubjects()->pluck('subjects.id')->toArray();

        $assessmentUserAverage = $assessmentUser
            ->where('assessment_id', "=", $this->assessment->id)
            ->whereHas("assessee", function (Builder $assessee) use ($instructorSubjects) {
                $assessee->where(function (Builder $assesseeSubQuery) use ($instructorSubjects) {
                    $assesseeSubQuery
                        ->where("type", "=", UserEnums::SCHOOL_INSTRUCTOR)
                        ->whereHas('schoolInstructorSubjects',function(Builder $schoolInstructorSubjects) use($instructorSubjects){
                            $schoolInstructorSubjects->whereIn('subject_id',$instructorSubjects);
                        });
                    })
                    ->orWhere("type", "!=", UserEnums::SCHOOL_INSTRUCTOR)
                ;
            })->average("score");

        return $assessmentUserAverage ?? 0.00;
    }

    private function getUserSchoolBranches(User $user) :array
    {
        if ($user->type == UserEnums::SCHOOL_ACCOUNT_MANAGER ) {
            $branches = $user
                ->schoolAccount()
                ->firstOrFail()
                ->branches()
                ->pluck("school_account_branches.id")
                ->toArray();
        }
        elseif ($user->type == UserEnums::EDUCATIONAL_SUPERVISOR ) {
            $branches = $user->branches()->pluck("school_account_branches.id")->toArray();
        } else {
            $branches = [$user->schoolAccountBranchType->id ?? ''];
        }

        return $branches;
    }
}
