<?php

namespace Database\Factories;

use App\OurEdu\Feedbacks\Feedback;
use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbacksFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Feedback::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'feedback' => $this->faker->text,
            'approved' => $this->faker->boolean,
            'student_id' => (Student::count() > 0 ? (Student::first()->id) : Student::factory()->create()->id)
        ];
    }
}
