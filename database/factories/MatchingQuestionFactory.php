<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatchingQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MatchingQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dataId = MatchingData::first()->id ?? MatchingData::factory()->create()->id;
        
        return [
            'text'    =>    randomEquation(),
            'res_matching_data_id'    =>    $dataId,
        ];
    }
}
