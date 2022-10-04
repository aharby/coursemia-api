<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompleteAnswerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompleteAnswer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'answer'    =>    $this->faker->word(1),
            'res_complete_question_id'    =>    CompleteQuestion::first()->id ?? CompleteQuestion::factory()->create()->id,
        ];
    }
}
