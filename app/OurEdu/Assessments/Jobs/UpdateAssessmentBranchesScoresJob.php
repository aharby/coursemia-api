<?php


namespace App\OurEdu\Assessments\Jobs;


use App\OurEdu\Assessments\Models\Assessment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAssessmentBranchesScoresJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Assessment
     */
    private $assessment;


    /**
     * UpdateAssessmentBranchesScoresJob constructor.
     * @param Assessment $assessment
     */
    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
    }

    public function handle()
    {
        $schoolBranches = $this->assessment->schoolAccount()->first();
        $schoolBranches = $schoolBranches->branches ?? [];

        $assessmentBranchesScores = [];

        foreach ($schoolBranches as $branch) {
            $assessmentBranchScore = $this->assessment->assessmentUsers()->finished()
                ->whereHas('assessee',function(Builder $assessorQuery) use($branch){
                    $assessorQuery->whereHas("branches", function (Builder $builder) use ($branch) {
                        $builder->where("school_account_branches.id", "=", $branch->id);
                    })->orWhereHas('schoolAccount.branches',function(Builder $builder)use($branch){
                        $builder->where("school_account_branches.id", "=", $branch->id);
                    })->orWhere('branch_id', "=", $branch->id);
                })
                ->average("score");

            $assessmentBranchesScores[$branch->id] = ['score' => $assessmentBranchScore ?? 0.00];
        }

        $this->assessment->assessmentBranchesScores()->sync($assessmentBranchesScores);
    }
}
