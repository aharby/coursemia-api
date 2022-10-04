<?php

namespace Database\Seeders;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Seeder;

class DeleteRepeatedStudentAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $generalQuizStudents = GeneralQuizStudent::query()
            ->with('generalQuiz')
            ->where('score_percentage', '>', 100)
            ->get();

        foreach ($generalQuizStudents as $generalQuizStudent) {
            $repeatedAnswers = GeneralQuizStudentAnswer::query()
                ->where('student_id', '=', $generalQuizStudent->student_id)
                ->where('general_quiz_id', '=', $generalQuizStudent->general_quiz_id)
                ->whereExists(
                    function (Builder $nestedQuery) use ($generalQuizStudent) {
                        $nestedQuery->select('general_quiz_question_id')
                            ->from('general_quiz_student_answers as dumpTable')
                            ->whereRaw('dumpTable.general_quiz_question_id = general_quiz_student_answers.general_quiz_question_id')
                            ->where('student_id', '=', $generalQuizStudent->student_id)
                            ->where('general_quiz_id', '=', $generalQuizStudent->general_quiz_id)
                            ->groupBy('general_quiz_question_id')
                            ->havingRaw('count(*) > 1');
                    }
                )
                ->orderByDesc('general_quiz_question_id')
                ->orderByDesc('is_correct')
                ->orderByDesc('score')
                ->get();

            /**
             * delete repeated answers of the single question
             */
            $oldQuestionId = -1;
            foreach ($repeatedAnswers as $repeatedAnswer) {
                if ($repeatedAnswer->general_quiz_question_id != $oldQuestionId) {
                    $oldQuestionId = $repeatedAnswer->general_quiz_question_id;
                    continue;
                }

                $repeatedAnswer->delete();
            }


            /**
             * update GeneralQuizStudent's score and percentage after delete repeated questions
             */
            $mark = $generalQuizStudent->generalQuiz->mark ?? 0;
            $score= GeneralQuizStudentAnswer::query()
                ->where('student_id', $generalQuizStudent->student_id)
                ->where('general_quiz_id', '=', $generalQuizStudent->general_quiz_id)
                ->where('is_correct', 1)
                ->sum('score');

            $scorePercentage = $mark ? ($score / $mark) * 100 : 0;
            $data = [
                'score_percentage'    =>  number_format($scorePercentage, 2, '.', ''),
                'score' => $score
            ];
            GeneralQuizStudent::query()->where('id', '=', $generalQuizStudent->id)->update($data);
        }
    }
}
