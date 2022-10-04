<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrueFalseQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrueFalseQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'text'    =>    randomEquation(),
            'image'    =>    'https://via.placeholder.com/500x500',
            'is_true'    =>    $this->faker->boolean(),
            'res_true_false_data_id'    =>    TrueFalseData::first()->id ?? TrueFalseData::factory()->create()->id,
            'time_to_solve'    =>    ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
        ];
    }
}
