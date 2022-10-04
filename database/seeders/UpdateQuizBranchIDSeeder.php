<?php

namespace Database\Seeders;

use App\OurEdu\Quizzes\Quiz;
use Illuminate\Database\Seeder;

class UpdateQuizBranchIDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quizzes = Quiz::query()
            ->with("creator")
            ->whereNull("branch_id")
            ->get();

        foreach ($quizzes as $quiz) {
            $quiz->branch_id = $quiz->creator->branch_id ?? null;
            $quiz->save();
        }
    }
}
