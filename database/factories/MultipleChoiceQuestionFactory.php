<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class MultipleChoiceQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MultipleChoiceQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'question'    =>    randomEquation(),
            'url'    =>    $this->faker->words(20, true),
            'res_multiple_choice_data_id'    =>    MultipleChoiceData::first()->id ?? MultipleChoiceData::factory()->create()->id,
            'time_to_solve'    =>   ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
        ];
    }
}
