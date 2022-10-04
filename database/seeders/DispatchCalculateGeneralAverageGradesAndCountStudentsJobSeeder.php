<?php

namespace Database\Seeders;

use App\OurEdu\BaseNotification\Jobs\CalculateGeneralAverageGradesAndCountStudentsJob;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DispatchCalculateGeneralAverageGradesAndCountStudentsJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $generalQuizzes = GeneralQuiz::query()
            ->where("end_at", "<", Carbon::now())
            ->get();

        foreach ($generalQuizzes as $quiz) {
            CalculateGeneralAverageGradesAndCountStudentsJob::dispatch($quiz);
        }
    }
}
