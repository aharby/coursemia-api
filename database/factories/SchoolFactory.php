<?php

namespace Database\Factories;

use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\Schools\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = School::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $country = (Country::count()) > 0 ? Country::first() : Country::factory()->create();
        $educationalSystem = EducationalSystem::factory()->create(['country_id' => $country->id]);

        return [
            'name:en' => $this->faker->name,
            'name:ar' => $this->faker->name,
            'country_id' => $country->id,
            'educational_system_id' => $educationalSystem->id,
            'address' => $this->faker->address,
            'email' => $this->faker->email,
            'mobile' => '01001199' . rand(111, 999),
            'is_active' => $this->faker->boolean(50),
        ];
    }
}
