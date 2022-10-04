<?php

namespace Database\Factories;

use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\SubjectPackages\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectPackageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Package::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'price' => $this->faker->numberBetween(0, 1000),
            'educational_system_id' => EducationalSystem::first()->id ?? EducationalSystem::factory()->create()->id,
            'country_id' => Country::first()->id ?? Country::factory()->create()->id,
            'grade_class_id' =>  GradeClass::first()->id ?? GradeClass::factory()->create()->id,
            'is_active' => 1,
            'academical_years_id' =>  Option::where('type', OptionsTypes::ACADEMIC_YEAR)->first()->id ?? Option::factory()->create(['type' => OptionsTypes::ACADEMIC_YEAR])->id,
        ];
    }
}
