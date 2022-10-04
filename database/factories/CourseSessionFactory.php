<?php

namespace Database\Factories;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseSessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseSession::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'course_id'    =>    Course::first()->id ?? Course::factory()->create()->id,
            'date'    =>    now()->addDays(random_int(2, 40))->format('Y-m-d'),
            'content'    =>    $this->faker->sentence(),
            'start_time'    =>    $this->faker->time(),
            'end_time'    =>    $this->faker->time(),
        ];
    }
}
