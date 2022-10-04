<?php

namespace Database\Factories;

use App\OurEdu\AcademicYears\AcademicYear;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicYearFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AcademicYear::class;

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
            'educational_system_id' => EducationalSystem::count() > 0 ? (EducationalSystem::first()->id) : EducationalSystem::factory()->create()->id,
            'end_date' => $this->faker->date('Y-m-d'),
            'is_active' => $this->faker->boolean(50),
        ];
    }
}
