<?php

namespace Database\Factories;

use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

class MultipleChoiceDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MultipleChoiceData::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description'    => $this->faker->words(5, true),
            'resource_subject_format_subject_id'    =>    ResourceSubjectFormatSubject::first()->id ?? ResourceSubjectFormatSubject::factory()->create()->id,
            'multiple_choice_type'    =>    Option::where('type', OptionsTypes::MULTI_CHOICE_MULTIPLE_CHOICE_TYPE)->first()->id ?? Option::factory()->create(['type' => OptionsTypes::MULTI_CHOICE_MULTIPLE_CHOICE_TYPE])->id,
        ];
    }
}
