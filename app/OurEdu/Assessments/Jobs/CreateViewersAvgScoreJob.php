<?php


namespace App\OurEdu\Assessments\Jobs;


use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateViewersAvgScoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Assessment
     */
    private $assessment;

    /**
     * FinishAssessmentsJob constructor.
     * @param Assessment $assessment
     */
    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
    }

    public function handle(AssessmentUsersRepositoryInterface $assessmentUsersRepository)
    {
        foreach ($this->assessment->resultViewers as $viewer) {
            $assessors = $assessmentUsersRepository->getAssessmentAssessors($this->assessment, false, $viewer);

            foreach ($assessors as $assessor) {
                $assessmentUsers = $assessmentUsersRepository->getAssessedUsersOfAssessor(
                    $this->assessment,
                    $assessor->user_id,
                    $viewer
                );

                $avgScore = $assessmentUsers
                    ->where('assessee_id', '!=', $viewer->id)
                    ->average('score');

                $avgTotalMark = $assessmentUsers
                    ->where('assessee_id', '!=', $viewer->id)
                    ->average('total_mark');

                $viewer->assessorViewerAvgScores()->updateOrCreate(
                    [
                        'assessment_id' => $this->assessment->id,
                        'assessor_id' => $assessor->user_id,
                        'viewer_id' => $viewer->id,
                    ],
                    [
                        'average_score' => $avgScore ?? 0,
                        'average_total_mark' => $avgTotalMark ?? 0
                    ]
                );
            }
        }
    }
}
