<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class MultiMatchingQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MultiMatchingQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dataId = MultiMatchingData::first()->id ?? MultiMatchingData::factory()->create()->id;

        return [
            'text'    =>    randomEquation(),
            'res_multi_matching_data_id'    =>    $dataId,
        ];
    }
}
