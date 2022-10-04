<?php

namespace Database\Factories;

use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(4),
            'type' => array_random(CourseEnums::getTypes()),
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addWeek()->format('Y-m-d'),
            'subscription_cost' => $this->faker->randomFloat(),
            'subject_id' => Subject::first()->id ?? Subject::factory()->create()->id,
            'instructor_id' => User::where('type', UserEnums::INSTRUCTOR_TYPE)->first()->id ?? User::factory()->create(['type' => UserEnums::INSTRUCTOR_TYPE])->id,
            'is_active' => 1,
        ];
    }
}
