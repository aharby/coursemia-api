<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalOption;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalQuestion;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalRecomendation;

class GeneratePsychologicalTestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tests = PsychologicalTest::factory()->count(5)->create();

        $tests->each(function ($test) {
            $test->recomendations()->save(PsychologicalRecomendation::factory()->make(['from' => 0, 'to' => 25]));
            $test->recomendations()->save(PsychologicalRecomendation::factory()->make(['from' => 26, 'to' => 50]));
            $test->recomendations()->save(PsychologicalRecomendation::factory()->make(['from' => 51, 'to' => 75]));
            $test->recomendations()->save(PsychologicalRecomendation::factory()->make(['from' => 76, 'to' => 100]));


            $test->options()->saveMany(PsychologicalOption::factory()->count(4)->make());

            $test->questions()->saveMany(PsychologicalQuestion::factory()->count(5)->make());
        });

        dump($tests->pluck('id'));
    }
}
