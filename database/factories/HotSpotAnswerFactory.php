<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class HotSpotAnswerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HotSpotAnswer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'answer'    =>    $this->faker->word(1),
            'res_hot_spot_question_id'    =>    HotSpotQuestion::first()->id ?? HotSpotQuestion::factory()->create()->id,
        ];
    }
}
