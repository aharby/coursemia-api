<?php

namespace Database\Seeders;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemarkGeneralQuizStudentsGradsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $generalQuizzes = GeneralQuiz::query()->where('id','2212')
            ->whereNotNull("published_at")
            ->get();

        foreach ($generalQuizzes as $quiz) {

            $mark = $quiz->mark;

            $scores= GeneralQuizStudentAnswer::query()
                ->where('general_quiz_id', $quiz->id)
                ->where('is_correct' , 1)
                ->groupBy('student_id')
                ->select("student_id", DB::raw("SUM(score) as score"))
                ->get()
                ->toArray();

            foreach ($scores as $userScore) {

                $score = $userScore["score"];
                $score_percentage = $mark ? ($score / $mark) * 100 : 0;
                $data = [
                    'score_percentage'    =>  number_format($score_percentage, 2, '.', ''),
                    'score' => $score
                ];

                GeneralQuizStudent::query()
                    ->where("student_id", "=", $userScore["student_id"])
                    ->where("general_quiz_id", "=", $quiz->id)
                    ->update($data);
            }
        }
    }
}
