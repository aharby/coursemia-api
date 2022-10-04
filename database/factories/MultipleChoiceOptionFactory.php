<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class MultipleChoiceOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MultipleChoiceOption::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'answer' => $this->faker->words(5, true),
            'is_correct_answer' => random_int(0, 1),
        ];
    }
}
