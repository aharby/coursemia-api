<?php

namespace Database\Factories;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Ratings\Rating;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rating::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $course = Course::factory()->create();

        return [
            'user_id'    =>    User::factory()->create()->id,
            'instructor_id'    =>    User::factory()->create(['type' => UserEnums::INSTRUCTOR_TYPE])->id,
            'rating'    =>    random_int(1, 5),
            'comment'    =>    $this->faker->sentence(8),
            'ratingable_type'    =>    get_class($course),
            'ratingable_id'    =>    $course->id,
        ];
    }
}
