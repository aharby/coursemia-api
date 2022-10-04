<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompleteQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompleteQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'question'    =>    randomEquation(),
            'res_complete_data_id'    =>    CompleteData::first()->id ?? CompleteData::factory()->create()->id,
            'time_to_solve'    =>    ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
        ];
    }
}
