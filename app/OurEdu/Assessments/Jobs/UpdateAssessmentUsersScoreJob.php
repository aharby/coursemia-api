<?php


namespace App\OurEdu\Assessments\Jobs;


use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentAnswer;
use App\OurEdu\Assessments\Models\AssessmentAssessor;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\Assessments\Models\AssessmentUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAssessmentUsersScoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Assessment
     */
    private $assessment;

    /**
     * @var AssessmentUser
     */
    private $assessmentUser;

    private $assessmentAssessor;

    /**
     * UpdateAssessmentUsersScoreJob constructor.
     * @param Assessment $assessment
     * @param AssessmentUser $assessmentUser
     */
    public function __construct(Assessment $assessment,AssessmentUser $assessmentUser,AssessmentAssessor $assessmentAssessor)
    {
        $this->assessment = $assessment;
        $this->assessmentUser = $assessmentUser;
        $this->assessmentAssessor = $assessmentAssessor;
    }

    public function handle()
    {
        // get score of skipped questions
        $skippedQuestionsGrades = AssessmentQuestion::query()
            ->where("skip_question", "=", true)
            ->whereHas(
                "assessorsAnswers",
                function (Builder $assessorsAnswers) {
                    $assessorsAnswers->where('assessment_user_id',$this->assessmentUser->id)
                        ->where('assessee_id',$this->assessmentUser->assessee_id);
                }
            )
            ->sum("question_grade") ?? 0.0;



        $this->assessmentUser->score = AssessmentAnswer::query()
            ->where('assessment_user_id',$this->assessmentUser->id)
            ->where('assessee_id',$this->assessmentUser->assessee_id)
            ->sum('score') ?? 0;

        $this->assessmentUser->total_mark = $this->assessmentUser->assessment->mark + $skippedQuestionsGrades;
        $this->assessmentUser->save();

        $userAssessmentsAverageScore = AssessmentUser::query()
            ->where('assessment_id', $this->assessment->id)
            ->where("user_id", $this->assessmentUser->user_id)
            ->finished()
            ->average('score');

        $averageTotalMark = AssessmentUser::query()
            ->where('assessment_id', $this->assessment->id)
            ->where("user_id", $this->assessmentUser->user_id)
            ->finished()
            ->average('total_mark');

        if($userAssessmentsAverageScore) {
            $this->assessmentAssessor->average_score = $userAssessmentsAverageScore;
            $this->assessmentAssessor->average_total_mark = $averageTotalMark;
            $this->assessmentAssessor->save();
        }

        // update the assessment's attributes that depends on values the assessment_users table
        UpdateAssessAvgScoreAfterFinishJob::dispatch($this->assessment);
    }
}
