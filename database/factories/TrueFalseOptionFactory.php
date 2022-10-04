<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class TrueFalseOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrueFalseOption::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'option' => Arr::random(['True', 'False']),
            'is_correct_answer' => random_int(0, 1),
        ];
    }
}
