<?php

namespace Database\Factories;

use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;
use Illuminate\Database\Eloquent\Factories\Factory;

class PsychologicalTestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PsychologicalTest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name:en'    =>    $this->faker->sentence(3),
            'name:ar'    =>    $this->faker->sentence(3),
            'instructions:en'    =>    $this->faker->paragraph(),
            'instructions:ar'    =>    $this->faker->paragraph(),
            'is_active'    =>    $this->faker->boolean()
        ];
    }
}
