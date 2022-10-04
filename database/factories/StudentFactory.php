<?php

namespace Database\Factories;

use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\Schools\School;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'wallet_amount' => $this->faker->numberBetween(10, 5000),
            'educational_system_id' => EducationalSystem::first()->id ?? EducationalSystem::factory()->create()->id,
            'class_id' =>  GradeClass::first()?->id,
            'academical_year_id' =>  Option::where('type', OptionsTypes::ACADEMIC_YEAR)->first()->id ?? Option::factory()->create(['type' => OptionsTypes::ACADEMIC_YEAR]),
            'user_id' =>  User::factory()->create(['type' => UserEnums::STUDENT_TYPE])->id,
            'school_id' => School::first()->id ?? School::factory()->create()->id,
        ];
    }
}
