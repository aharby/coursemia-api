<?php


namespace App\OurEdu\Assessments\Jobs;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateTotalAssesseCountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $assessment;
    private $assessorId;
    private $assesseeId;
    private $assessmentUsersRepo;


    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
        $this->assessmentUsersRepo = app(AssessmentUsersRepositoryInterface::class);
    }

    public function handle()
    {
        $viewers = $this->assessment->resultViewers;
        $this->assessment->assessed_assesses_count = $this->assessment->assessmentUsers()
            ->finished()
            ->distinct('assessee_id')
            ->count('assessee_id');

        $this->assessment->total_assesses_count =   $this->assessment->assessees->count();
        $this->assessment->save();

        foreach ($viewers as $viewer) {
            $assesses = $this->assessmentUsersRepo->getAllAssesseeByViewerId($this->assessment, $viewer, false)->count();
            if ($assesses > 0) {
                $viewer->assessmentsAsViewer()->updateExistingPivot($this->assessment->id,['total_assesses_count' =>  $assesses] );
            }
        }
    }
}
