<?php

namespace Database\Factories;

use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationalClassFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GradeClass::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title:en' => $this->faker->firstName,
            'title:ar' => $this->faker->firstName,
            'country_id' => Country::count() > 0 ? (Country::first()->id) : Country::factory()->create()->id,
            'educational_system_id' => EducationalSystem::count() > 0 ? (EducationalSystem::first()->id) : EducationalSystem::factory()->create()->id,
            'is_active' => $this->faker->boolean(50),
        ];
    }
}
