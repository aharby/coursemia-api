<?php

namespace Database\Seeders;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use Illuminate\Database\Seeder;

class AddSectionIdToStudentQuizAnswersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $answers = GeneralQuizStudentAnswer::query()
            ->with("questionBank")
            ->whereNull("subject_format_subject_id")
            ->get();


        foreach ($answers as $answer) {
            if ($answer->questionBank && !is_null($answer->questionBank->subject_format_subject_id)) {
                $answer->subject_format_subject_id = $answer->questionBank->subject_format_subject_id;
                $answer->save();
            }
        }
    }
}
