<?php

namespace Database\Seeders;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use Illuminate\Database\Seeder;

class showResultsStudentGeneralQuizzesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $students = GeneralQuiz::query()->where('show_result', false)->with('studentsAnswered')->get()->pluck('studentsAnswered')->flatten();
        foreach ($students as $student) {
            $student->update(['show_result'=> false]);
        }
    }
}
