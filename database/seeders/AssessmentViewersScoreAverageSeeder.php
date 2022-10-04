<?php
namespace Database\Seeders;

use App\OurEdu\Assessments\Jobs\UpdateAssessAvgScoreAfterFinishJob;
use App\OurEdu\Assessments\Models\Assessment;
use Illuminate\Database\Seeder;

class AssessmentViewersScoreAverageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $assessments = Assessment::query()->get();

        foreach ($assessments as $assessment) {
            UpdateAssessAvgScoreAfterFinishJob::dispatch($assessment);
        }
    }
}
