<?php

namespace Database\Seeders;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Seeder;

class ValidateGeneralQuizMarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $generalQuizzes = GeneralQuiz::query()
            ->get();

        foreach ($generalQuizzes as $generalQuiz) {
            $quizMark = $generalQuiz->questions()->pluck('grade')->sum();
            if ($generalQuiz->mark == $quizMark) {
                continue;
            }
            $generalQuiz->mark = $quizMark;
            $generalQuiz->save();

            $generalQuizStudents = GeneralQuizStudent::query()
                ->with('generalQuiz')
                ->where('general_quiz_id', '=', $generalQuiz->id)
                ->get();

            foreach ($generalQuizStudents as $generalQuizStudent) {
                /**
                 * update GeneralQuizStudent's score and percentage after delete repeated questions
                 */
                $mark = $generalQuizStudent->generalQuiz->mark ?? 0;
                $score = GeneralQuizStudentAnswer::query()
                    ->where('student_id', $generalQuizStudent->student_id)
                    ->where('general_quiz_id', '=', $generalQuizStudent->general_quiz_id)
                    ->where('is_correct', 1)
                    ->sum('score');

                $scorePercentage = $mark ? ($score / $mark) * 100 : 0;
                $data = [
                    'score_percentage' => number_format($scorePercentage, 2, '.', ''),
                    'score' => $score
                ];
                GeneralQuizStudent::query()->where('id', '=', $generalQuizStudent->id)->update($data);
            }
        }
    }
}
