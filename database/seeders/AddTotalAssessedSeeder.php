<?php

namespace Database\Seeders;

use App\OurEdu\Assessments\Jobs\UpdateAssesseCountJob;
use App\OurEdu\Assessments\Models\Assessment;
use Illuminate\Database\Seeder;

class AddTotalAssessedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $assessments =  Assessment::query()
            ->whereHas("resultViewers")->get();

       foreach ($assessments as $assessment){
           UpdateAssesseCountJob::dispatch($assessment);
       }
    }
}
