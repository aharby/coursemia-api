<?php


namespace App\OurEdu\Assessments\Jobs;


use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentAnswer;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateQuestionsBranchesScoresJob implements ShouldQueue
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

        foreach ($schoolBranches as $branch) {
            $branchAssessmentUsersIDs = $this->assessment->assessmentUsers()->finished()
                ->whereHas('assessee',function(Builder $assessorQuery) use($branch){
                    $assessorQuery->whereHas("branches", function (Builder $builder) use ($branch) {
                        $builder->where("school_account_branches.id", "=", $branch->id);
                    })->orWhereHas('schoolAccount.branches',function(Builder $builder)use($branch){
                        $builder->where("school_account_branches.id", "=", $branch->id);
                    })->orWhere('branch_id', "=", $branch->id);
                })->pluck("id")->toArray();

            $questionBranchScore = AssessmentAnswer::query()
                ->whereIn("assessment_user_id", $branchAssessmentUsersIDs)
                ->where("assessment_id", "=", $this->assessment->id)
                ->groupBy("assessment_question_id")
                ->selectRaw("assessment_question_id, avg(score) as score")
                ->pluck("score", "assessment_question_id")
                ->toArray();

            $questionsScores = [];

            foreach ($questionBranchScore as $questionId => $score) {
                $questionsScores[$questionId] = ["score" => $score?? 0.00];
            }

            $branch->assessmentQuestion()->syncWithoutDetaching($questionsScores);
        }

        $noneSkippedQuestionScores = AssessmentAnswer::query()
            ->whereHas(
                "assessmentQuestion",
                function (Builder $assessmentQuestion) {
                    $assessmentQuestion->where("skip_question", "=", false);
                }
            )
            ->where("assessment_id", "=", $this->assessment->id)
            ->groupBy("assessment_question_id")
            ->selectRaw("assessment_question_id, sum(score) as score")
            ->pluck("score", "assessment_question_id")
            ->toArray();

        $assessmentSolvedTimes = $this->assessment->assessmentUsers()->count();

        foreach ($noneSkippedQuestionScores as $questionId => $score) {
            $scoreAverage = $assessmentSolvedTimes > 0 ? $score/$assessmentSolvedTimes : 0.00;
            AssessmentQuestion::query()
                ->where("id", "=", $questionId)
                ->update(["average_score" => number_format($scoreAverage, 2)]);
        }

        $skippedQuestionScores = AssessmentAnswer::query()
            ->whereHas(
                "assessmentQuestion",
                function (Builder $assessmentQuestion) {
                    $assessmentQuestion->where("skip_question", "=", true);
                }
            )
            ->where("assessment_id", "=", $this->assessment->id)
            ->groupBy("assessment_question_id")
            ->selectRaw("assessment_question_id, avg(score) as score")
            ->pluck("score", "assessment_question_id")
            ->toArray();

        foreach ($skippedQuestionScores as $questionId => $score) {
            AssessmentQuestion::query()
                ->where("id", "=", $questionId)
                ->update(["average_score" => $score ?? 0.00]);
        }
    }
}
