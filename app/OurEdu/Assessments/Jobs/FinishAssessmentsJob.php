<?php


namespace App\OurEdu\Assessments\Jobs;


use App\OurEdu\Assessments\Models\Assessment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FinishAssessmentsJob implements ShouldQueue
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

    public function handle()
    {
        $userAssessments = $this->assessment
            ->assessmentUsers()
            ->where("is_finished", "!=", 1)->get();
        
        foreach ($userAssessments as $userAssessment) {
            $userAssessment->is_finished = 1;
            $userAssessment->end_at = now();
            $userAssessment->save();
        }
        
        $userFinishedAssessAvgScore = $this->assessment
            ->assessmentUsers()
            ->where("is_finished", 1)->average('score');
        $this->assessment->average_score = $userFinishedAssessAvgScore;
        $this->assessment->save();
    }
}
