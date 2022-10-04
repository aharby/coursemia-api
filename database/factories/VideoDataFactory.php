<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideoDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VideoData::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'video_type'    =>    'upload',
            'title'    =>    $this->faker->title(),
            'description'    =>    $this->faker->words(5, true),
            'resource_subject_format_subject_id'    =>    ResourceSubjectFormatSubject::first()->id ?? ResourceSubjectFormatSubject::factory()->create()->id
        ];
    }
}
