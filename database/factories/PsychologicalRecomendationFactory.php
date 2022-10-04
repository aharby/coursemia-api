<?php

namespace Database\Factories;

use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalRecomendation;
use Illuminate\Database\Eloquent\Factories\Factory;

class PsychologicalRecomendationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PsychologicalRecomendation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'result:ar'    =>    $this->faker->sentence(2),
            'result:en'    =>    $this->faker->sentence(2),
            'recomendation:ar'    =>    $this->faker->paragraph(),
            'recomendation:en'    =>    $this->faker->paragraph(),
            'from'    =>    random_int(0, 50),
            'to'    =>    random_int(50, 100),
            'psychological_test_id'    =>    PsychologicalTest::factory()->create()->id,
            'is_active'    =>    $this->faker->boolean(),
        ];
    }
}
