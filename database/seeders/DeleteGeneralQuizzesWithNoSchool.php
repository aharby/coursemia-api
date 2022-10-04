<?php

namespace Database\Seeders;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteGeneralQuizzesWithNoSchool extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quizzes = GeneralQuiz::doesntHave('school')->get();
        try {
            foreach ($quizzes as $quiz) {
                DB::table('classroom_general_quiz')->where('general_quiz_id', $quiz->id)->delete();
                DB::table('general_quiz_user')->where('general_quiz_id', $quiz->id)->delete();
                DB::table('general_quiz_subject_format_subject')->where('general_quiz_id', $quiz->id)->delete();
                DB::table('general_quiz_question')->where('general_quiz_id', $quiz->id)->delete();
                DB::table('general_quiz_students')->where('general_quiz_id', $quiz->id)->delete();
                $answers = DB::table('general_quiz_student_answers')->where('general_quiz_id', $quiz->id)->get();
                foreach ($answers as $answer) {
                    DB::table('generalquiz_student_answer_details')->where('main_answer_id', $answer->id)->delete();
                    $answer->delete();
                }
                $quiz->forceDelete();
            }
            DB::table('general_quiz_question_bank')->where('school_account_id', null)->delete();
        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }
}
