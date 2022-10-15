<?php

namespace Database\Seeders;

use App\Modules\Courses\Models\Answer;
use App\Modules\Courses\Models\Category;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\Question;
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
        $courses = Course::get();
        foreach ($courses as $course) {
            for ($i = 1; $i <= 10; $i++) {
                $category = Category::inRandomOrder()->first();
                $question = [
                    'course_id' => $course->id,
                    'title:en' => "English Question number $i",
                    'description:ar' => "السؤال رقم $i",
                    'description:en' => "English Question number $i",
                    'title:ar' => "السؤال رقم $i",
                    'image' => 'questions/question-1665005192.png',
                    'explanation:en' => "English Explanation for question number $i",
                    'explanation:ar' => "شرح السؤال رقم $i",
                    'explanation_image' => 'questions/question-1665005192.png',
                    'explanation_voice' => 'questions/file_example_WAV_10MG.wav',
                ];
                $question = Question::create($question);
                $randCorrectIndex = rand(1, 4);
                for ($index = 1; $index <= 4; $index++) {
                    $answer = [
                        'question_id' => $question->id,
                        'answer:en' => "Answer number $index for question number $i",
                        'answer:ar' => "الاجابة رقم $index للسؤال رقم $i",
                        'is_correct' => $index == $randCorrectIndex,
                        'chosen_percentage' => rand(1, 100)
                    ];
                    Answer::create($answer);
                }
            }
        }
    }
}
