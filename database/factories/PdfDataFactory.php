<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfData;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

class PdfDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PdfData::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'pdf_type'    =>    'upload',
            'title'    =>    $this->faker->title(),
            'description'    =>    $this->faker->words(5, true),
            'resource_subject_format_subject_id'    =>    ResourceSubjectFormatSubject::first()->id ?? ResourceSubjectFormatSubject::factory()->create()->id
        ];
    }
}
