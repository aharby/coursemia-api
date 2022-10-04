<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlashDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FlashData::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'    =>    $this->faker->title(),
            'description'    =>    $this->faker->words(5, true),
            'resource_subject_format_subject_id'    =>    ResourceSubjectFormatSubject::first()->id ?? ResourceSubjectFormatSubject::factory()->create()->id
        ];
    }
}
