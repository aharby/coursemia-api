<?php

namespace Database\Factories;

use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Courses\Models\SubModels\LiveSession;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Factories\Factory;

class LiveSessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LiveSession::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(4),
            'type'    =>    CourseEnums::LIVE_SESSION,
            'subscription_cost' => $this->faker->randomFloat(),
            'subject_id' => Subject::first()->id ?? Subject::factory()->create()->id,
            'instructor_id' => User::factory()->create(['type' => UserEnums::INSTRUCTOR_TYPE])->id,
            'is_active' => $this->faker->boolean(),
        ];
    }
}
