<?php

namespace Database\Factories;

use App\OurEdu\Schools\School;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstructorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Instructor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'hire_date'    =>    now(),
            'user_id'    =>    User::factory()->create(['type' => UserEnums::INSTRUCTOR_TYPE])->id,
            'school_id'    =>    School::first()->id ?? School::factory()->create()->id,
        ];
    }
}
