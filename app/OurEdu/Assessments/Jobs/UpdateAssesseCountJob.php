<?php


namespace App\OurEdu\Assessments\Jobs;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAssesseCountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Assessment
     */
    private Assessment $assessment;


    /**
     * UpdateAssesseCountJob constructor.
     * @param Assessment $assessment
     */
    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
    }

    public function handle(AssessmentUsersRepositoryInterface $assessmentUsersRepo)
    {
        $this->assessment->load("assessees", "resultViewers", "creator");

        $this->assessment->total_assesses_count =  $this->assessment->assessees->count();
        $this->assessment->assessed_assesses_count =  $this->getViewerResultAssesseesCount($this->assessment->creator);
        $this->assessment->save();

        foreach ($this->assessment->resultViewers as $viewer) {
            $this->assessment->resultViewers()
                ->where("users.id", "=", $viewer->id)
                ->updateExistingPivot(
                    $viewer->id,
                    [
                        "assessed_assesses_count" => $this->getViewerResultAssesseesCount($viewer),
                    ]
                );
        }

    }

    private function getViewerResultAssesseesCount(User $viewer, $getTotal = false) :int
    {
        $branches = $this->getUserSchoolBranches($viewer);

        $assesseesCount = $this->assessment->assessees();

        if (!$getTotal) {
            $assesseesCount->whereHas("assessmentUserAsAssessee", function (Builder $assessmentUser) {
                $assessmentUser->where("assessment_id", "=", $this->assessment->id)->finished();
            });
        }

        $assesseesCount
            ->where("users.id", "!=", $viewer->id)
            ->where(function(Builder $assesseeQuery) use($branches){
                $assesseeQuery->whereHas("branches", function (Builder $builder) use ($branches) {
                    $builder->whereIn("school_account_branches.id", $branches);
                })->orWhereHas('schoolAccount.branches',function(Builder $builder)use($branches){
                    $builder->whereIn("school_account_branches.id", $branches);
                })->orWhereIn('branch_id',$branches);
            });

        $functionName = $viewer->type."AssesseesCount";

        if (method_exists($this, $functionName)) {
            return $this->{$functionName}($viewer, $assesseesCount);
        }

        return $assesseesCount->count() ?? 0;
    }

    private function educational_supervisorAssesseesCount(User $viewer, BelongsToMany $assesseeBuilder) :int
    {
        $subjects = $viewer->educationalSupervisorSubjects()->pluck('subjects.id')->toArray();

        $assesseesCount = $assesseeBuilder
            ->where(function (Builder $assessee) use ($subjects) {
                $assessee->where(function(Builder $query) use ($subjects) {
                    $query->where(function(Builder $q) use ($subjects){
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
            ->count();

        return $assesseesCount ?? 0;
    }

    private function school_instructorAssesseesCount(User $viewer, BelongsToMany $assesseeBuilder) :int
    {
        $instructorSubjects = $viewer->schoolInstructorSubjects()->pluck('subjects.id')->toArray();

        $assesseesCount = $assesseeBuilder
            ->where(function (Builder $assessee) use ($instructorSubjects) {
                $assessee->where(function (Builder $assesseeSubQuery) use ($instructorSubjects) {
                    $assesseeSubQuery
                        ->where("type", "=", UserEnums::SCHOOL_INSTRUCTOR)
                        ->whereHas('schoolInstructorSubjects',function(Builder $schoolInstructorSubjects) use($instructorSubjects){
                            $schoolInstructorSubjects->whereIn('subject_id',$instructorSubjects);
                        });
                })
                    ->orWhere("type", "!=", UserEnums::SCHOOL_INSTRUCTOR);
            })
            ->count();

        return $assesseesCount ?? 0;
    }

    private function getUserSchoolBranches(User $user) :array
    {
        if ($user->type == UserEnums::SCHOOL_ACCOUNT_MANAGER ) {
            $branches = $user
                ->schoolAccount()
                ->first()
                ->branches()
                ->pluck("school_account_branches.id")
                ->toArray() ?? [];
        } elseif ($user->type == UserEnums::ASSESSMENT_MANAGER ) {
            $branches = $user
                ->school()
                ->first()
                ->branches()
                ->pluck("school_account_branches.id")
                ->toArray() ?? [];
        }
        elseif ($user->type == UserEnums::EDUCATIONAL_SUPERVISOR ) {
            $branches = $user->branches()->pluck("school_account_branches.id")->toArray();
        } else {
            $branches = [$user->schoolAccountBranchType->id ?? ''];
        }

        return $branches;
    }
}
