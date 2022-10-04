<?php

namespace Database\Seeders;

use App\OurEdu\Exams\Models\Competitions\CompetitionStudent;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use Illuminate\Database\Seeder;

class UpdateCompetitionRankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $competitions = Exam::query()->has('competitionStudents')->get();
       foreach ($competitions as $competition){
           $repo = new ExamRepository($competition);
           $repo->updateStudentsRankInCompetition($competition);
        }
    }
}
