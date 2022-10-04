<?php

namespace Database\Seeders;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateStudentsGeneralQuizOrder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GeneralQuiz::query()
            ->whereNotNull('published_at')
            ->active()
            ->whereDate('end_at', '<', now())
            ->whereHas('studentsAnswered')
            ->chunk(100, function ($quizzes) {
                foreach ($quizzes as  $quiz) {
                    DB::statement("update general_quiz_students join (SELECT id, score, general_quiz_id,FIND_IN_SET( score, (
                    SELECT GROUP_CONCAT(DISTINCT score ORDER BY score DESC ) FROM general_quiz_students WHERE general_quiz_id = " . $quiz->id . ")
                     ) AS R FROM general_quiz_students WHERE general_quiz_id = " . $quiz->id . " ) AS K USING(id) SET general_quiz_students.order = K.R");
                }
            });
    }
}
