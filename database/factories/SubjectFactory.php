<?php

namespace Database\Factories;

use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subject::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->addWeek()->format('Y-m-d'),
            'subscription_cost' => 1,
            'is_active' => 1,
            'educational_system_id' => EducationalSystem::first()->id ?? EducationalSystem::factory()->create()->id,
            'country_id' => Country::first()->id ?? Country::factory()->create()->id,
            'grade_class_id' =>  GradeClass::first()?->id,
            'educational_term_id' =>  Option::where('type', OptionsTypes::EDUCATIONAL_TERM)->first()->id ?? Option::factory()->create(['type' => OptionsTypes::EDUCATIONAL_TERM])->id,
            'academical_years_id' =>  Option::where('type', OptionsTypes::ACADEMIC_YEAR)->first()->id ?? Option::factory()->create(['type' => OptionsTypes::ACADEMIC_YEAR])->id,
        ];
    }
}
