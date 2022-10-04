<?php

namespace Database\Factories;

use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationalSystemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EducationalSystem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name:en' => $this->faker->firstName,
            'name:ar' => $this->faker->firstName,
            'country_id' => Country::count() > 0 ? (Country::first()->id) : Country::factory()->create()->id,
            'is_active' => $this->faker->boolean(50),
        ];
    }
}
