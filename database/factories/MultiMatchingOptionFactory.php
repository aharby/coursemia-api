<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class MultiMatchingOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MultiMatchingOption::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dataId = MultiMatchingData::first()->id ?? MultiMatchingData::factory()->create()->id;
        return [
            'option' => $this->faker->words(5, true),
            'res_multi_matching_data_id'    =>    $dataId,
        ];
    }
}
