<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureData;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

class PictureDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PictureData::class;

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
