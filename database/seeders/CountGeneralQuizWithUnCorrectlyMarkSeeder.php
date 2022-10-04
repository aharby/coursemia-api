<?php

namespace Database\Seeders;

use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use Illuminate\Database\Seeder;

class CountGeneralQuizWithUnCorrectlyMarkSeeder extends Seeder
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

        $counts = [
            GeneralQuizTypeEnum::HOMEWORK => 0,
            GeneralQuizTypeEnum::PERIODIC_TEST => 0,
            GeneralQuizTypeEnum::FORMATIVE_TEST => 0,
            'total_count' => 0,
        ];

        foreach ($generalQuizzes as $generalQuiz) {
            $quizMark = $generalQuiz->questions()->pluck('grade')->sum();

            if ($generalQuiz->mark != $quizMark) {
                $counts[$generalQuiz->quiz_type] = $counts[$generalQuiz->quiz_type]+1;
                $counts['total_count'] = $counts['total_count']+1;
            }
        }

        dump($counts);
    }
}
