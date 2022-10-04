<?php

namespace Database\Factories;

use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class PsychologicalOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PsychologicalOption::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name:ar'    =>    $this->faker->sentence(3),
            'name:en'    =>    $this->faker->sentence(3),
            'points'    =>    array_random(range(0, 10)),
            'psychological_test_id'    =>    PsychologicalTest::factory()->create()->id,
            'is_active'    =>    true
        ];
    }
}
