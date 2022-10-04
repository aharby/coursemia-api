<?php

namespace Database\Seeders;

use App\OurEdu\Users\User;
use Illuminate\Database\Seeder;
use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalRecomendation;

class PsychologicalTestStatisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $test = PsychologicalTest::factory()->create();

        $test->recomendations()->saveMany(PsychologicalRecomendation::factory()->count(random_int(2, 6))->make());


        User::factory()->count(20)->create();

        User::get()->each(function ($user) use ($test) {
            $test->results()->create([
                'psychological_recomendation_id'    =>  $test->recomendations()->inRandomOrder()->first()->id,
                'user_id'   =>  $user->id,
                'percentage'    =>  random_int(1, 100)
            ]);
        });

        dump($test->id);
    }
}
