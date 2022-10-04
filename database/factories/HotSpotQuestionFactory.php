<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class HotSpotQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HotSpotQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'question'    =>    $this->faker->words(5, true) . '?',
            'image_width'    =>    777,
            'question_feedback'    =>    $this->faker->words(5, true),
            'time_to_solve'    =>    ResourcesConfigurationEnum::QUESTION_TIME_TO_SOLVE,
            'res_hot_spot_data_id'    =>    HotSpotData::first()->id ?? HotSpotData::factory()->create()->id,
        ];
    }
}
