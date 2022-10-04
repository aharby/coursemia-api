<?php

namespace Database\Seeders;

use App\OurEdu\Assessments\Jobs\UpdateAssessmentBranchesScoresJob;
use App\OurEdu\Assessments\Jobs\UpdateQuestionsBranchesScoresJob;
use App\OurEdu\Assessments\Models\Assessment;
use Illuminate\Database\Seeder;

class UpdateAssessmentQuestionScoresForBranches extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $assessments = Assessment::query()
            ->get();

        foreach ($assessments as $assessment) {
            UpdateQuestionsBranchesScoresJob::dispatch($assessment);
            UpdateAssessmentBranchesScoresJob::dispatch($assessment);
        }
    }
}
