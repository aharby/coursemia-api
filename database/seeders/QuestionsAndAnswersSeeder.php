<?php

namespace Database\Seeders;

use App\Modules\Questions\Models\Answer;
use App\Modules\Questions\Models\Question;
use Illuminate\Database\Seeder;

class QuestionsAndAnswersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++){
            $question = new Question;
            $question->question_en = "English Question number $i";
            $question->question_ar = "السؤال رقم $i";
            $question->image = 'uploads/questions/question-1665005192.png';
            $question->explanation_text_en = "English Explanation for question number $i";
            $question->explanation_text_ar = "شرح السؤال رقم $i";
            $question->explanation_image = 'uploads/questions/question-1665005192.png';
            $question->explanation_voice = 'https://google.com';
            $question->save();
            for ($answer = 1; $answer <= 4; $answer++){
                $ans = new Answer;
                $ans->question_id = $question->id;
                $ans->answer_en = "Answer number $answer for question number $i";
                $ans->answer_ar = "الاجابة رقم $answer للسؤال رقم $i";
                $ans->is_correct = rand(0,1);
                $ans->choosen_percentage = rand(1,100);
                $ans->save();
            }
        }
    }
}
