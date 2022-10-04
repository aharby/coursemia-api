<?php

namespace Database\Factories;

use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class PsychologicalQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PsychologicalQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name:ar'    =>    $this->faker->sentence(5) . "?",
            'name:en'    =>    $this->faker->sentence(5) . "?",
            'psychological_test_id'    =>    PsychologicalTest::factory()->create()->id,
            'is_active'    =>    true
        ];
    }
}
