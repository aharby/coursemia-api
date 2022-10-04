<?php

namespace Database\Seeders;

use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\Competitions\CompetitionStudent;
use App\OurEdu\Exams\Models\Exam;
use Illuminate\Database\Seeder;

class UpdateIsFinishedCompetiton extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CompetitionStudent::query()->whereHas('exam',function ($query){
            $query->whereIn('type',[ExamTypes::COURSE_COMPETITION,ExamTypes::COMPETITION])->where('is_finished',true);
        })->update(['is_finished'=>true]);
    }
}
