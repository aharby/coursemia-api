<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingOption;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatchingOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MatchingOption::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dataId = MatchingData::first()->id ?? MatchingData::factory()->create()->id;
        return [
            'option' => $this->faker->words(5, true),
            'res_matching_data_id'    =>    $dataId,
            'res_matching_question_id' =>  MatchingQuestion::where('res_matching_data_id', $dataId)->first()->id ?? MatchingQuestion::factory()->create(['res_matching_data_id' => $dataId])->id,
        ];
    }
}
